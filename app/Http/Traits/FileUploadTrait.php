<?php
namespace App\Http\Traits;

trait FileUploadTrait {

    public function saveInvoice($file)
    {
        try {
            $fileNameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filenameSimpan = $filename.'__'.time().'.'.$extension;
            $path = $file->storeAs('public/bukti_image', $filenameSimpan);
            return $filenameSimpan;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }
}