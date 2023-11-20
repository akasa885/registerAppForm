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

    public function getSubMemberParticapant(Request $request)
    {
        $request->validate([
            'parent_member' => 'required|exists:members,id',
        ]);
        $member = $request->parent_member;
        $member = \App\Models\Member::find($member);
        $particapants = $member->subMembers()->get();
        $view = view('admin.ajax.member.sub-member-list', compact('particapants'))->render();

        return new JsonResponse([
            'status' => 'success',
            'view' => $view,
        ]);
    }

    public function checkSession(Request $request)
    {
        $request->validate([
            'session' => 'required',
        ]);
        
        $session = $request->session;
        if ($session == 'auth') {
            if (auth()->check()) {
                return new JsonResponse([
                    'status' => 'success',
                    'message' => 'Session is valid',
                ]);
            } else {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Session is invalid',
                    'redirect' => route('admin.login'),
                ]);
            }
        } else {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Session is invalid',
            ]);
        }
    }
}
