<?php
namespace App\Http\Traits;

trait FileUploadTrait {

    public function saveInvoice($file, $innerPath = null)
    {
        try {
            $fileNameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filenameSimpan = $filename.'__'.time().'.'.$extension;
            if ($innerPath) {
                $path = $file->storeAs('public/bukti_image/'.$innerPath, $filenameSimpan);
                return $filenameSimpan;
            }
            $path = $file->storeAs('public/bukti_image', $filenameSimpan);
            return $filenameSimpan;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}