<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UniSharp\LaravelFilemanager\Controllers\UploadController;
use Illuminate\Http\JsonResponse;

class AjaxController extends Controller
{
    public function ckUploadImage(Request $request)
    {
        $lfm = new UploadController();
        $return = $lfm->upload();
        $CKEditorFuncNum = $request->input('ckCsrfToken');
        $msg = 'Image uploaded successfully'; 
        $orContent = $return->getOriginalContent();
        $tempFilename = explode('/', $orContent['url']);
        $filename = end($tempFilename);
        if (isset($orContent['uploaded'])) {
            $orContent['fileName'] = $filename;
        }

        return new JsonResponse($orContent);
    }
}
