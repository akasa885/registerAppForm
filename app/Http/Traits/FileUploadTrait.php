<?php

namespace App\Http\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

trait FileUploadTrait
{
    protected $invoiceImageQuality = 85;


    public function saveInvoice($file, $innerPath = null)
    {
        try {
            $realPath = $file->getRealPath();
            if (!$realPath || !is_file($realPath) || !is_readable($realPath)) {
                throw new \Exception('File upload tidak valid.');
            }

            $normalizedInnerPath = $this->normalizeInvoiceInnerPath($innerPath);
            $filePayload = $this->prepareInvoicePayload($realPath);

            $filenameSimpan = hash('sha256', $file->getClientOriginalName() . Str::random(32) . microtime(true)) . '.' . $filePayload['extension'];
            $storagePath = 'public/bukti_image' . ($normalizedInnerPath ? '/' . $normalizedInnerPath : '') . '/' . $filenameSimpan;

            if (!Storage::put($storagePath, $filePayload['contents'])) {
                throw new \Exception('Gagal menyimpan file upload.');
            }

            return $normalizedInnerPath ? $normalizedInnerPath . '/' . $filenameSimpan : $filenameSimpan;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    protected function prepareInvoicePayload($realPath)
    {
        $binary = file_get_contents($realPath);

        if ($binary === false || $binary === '') {
            throw new \Exception('File upload kosong atau tidak dapat dibaca.');
        }

        if ($this->isValidPdfUpload($binary)) {
            if ($this->containsActivePdfContent($binary)) {
                throw new \Exception('PDF mengandung konten aktif yang tidak diizinkan.');
            }

            return [
                'extension' => 'pdf',
                'contents' => $binary,
            ];
        }

        return $this->sanitizeImageUpload($binary);
    }

    protected function sanitizeImageUpload($binary)
    {
        $imageType = @exif_imagetype('data://application/octet-stream;base64,' . base64_encode($binary));
        if (!$imageType) {
            throw new \Exception('Tipe file tidak diizinkan. Hanya boleh upload gambar JPG, PNG, GIF, atau PDF.');
        }

        $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
        if (!in_array($imageType, $allowedTypes, true)) {
            throw new \Exception('Tipe file tidak diizinkan. Hanya boleh upload gambar JPG, PNG, GIF, atau PDF.');
        }

        $imageInfo = @getimagesizefromstring($binary);
        if ($imageInfo === false) {
            throw new \Exception('File gambar tidak valid.');
        }

        try {
            $imageManager = $this->createInvoiceImageManager();
            $image = $imageManager->make($binary)->orientate();
            $sanitizedBinary = (string) $image->encode('webp', $this->invoiceImageQuality);
        } catch (\Throwable $th) {
            throw new \Exception('File gambar tidak dapat diproses dengan aman.', 0, $th);
        }

        if ($sanitizedBinary === false || $sanitizedBinary === '') {
            throw new \Exception('File gambar tidak dapat disanitasi.');
        }

        return [
            'extension' => 'webp',
            'contents' => $sanitizedBinary,
        ];
    }

    protected function createInvoiceImageManager()
    {
        if (extension_loaded('imagick')) {
            return new ImageManager(['driver' => 'imagick']);
        }

        if (extension_loaded('gd')) {
            return new ImageManager(['driver' => 'gd']);
        }

        throw new \Exception('GD atau Imagick diperlukan untuk memproses file gambar dengan aman.');
    }

    protected function isValidPdfUpload($binary)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($binary);

        return $mimeType === 'application/pdf' && strpos($binary, '%PDF-') === 0;
    }

    protected function containsActivePdfContent($binary)
    {
        $blockedPatterns = [
            '/\\/JavaScript\b/i',
            '/\\/JS\b/i',
            '/\\/Launch\b/i',
            '/\\/RichMedia\b/i',
            '/\\/OpenAction\b/i',
            '/\\/AA\b/i',
        ];

        foreach ($blockedPatterns as $pattern) {
            if (preg_match($pattern, $binary) === 1) {
                return true;
            }
        }

        return false;
    }

    protected function normalizeInvoiceInnerPath($innerPath)
    {
        if (!$innerPath) {
            return null;
        }

        $normalizedPath = trim(str_replace('\\', '/', $innerPath), '/');

        if ($normalizedPath === '' || strpos($normalizedPath, '..') !== false) {
            throw new \Exception('Path penyimpanan upload tidak valid.');
        }

        return $normalizedPath;
    }
}
