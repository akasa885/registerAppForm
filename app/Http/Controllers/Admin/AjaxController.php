<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UniSharp\LaravelFilemanager\Controllers\UploadController;
use Illuminate\Http\JsonResponse;
use App\Models\Member;
use App\Models\Invoice;
use App\Models\Link;
use App\Models\MemberTrash;
use App\Models\OrderDetail;

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

    public function getMemberUploadBuktiTransfer(Request $request)
    {
        if (!$request->expectsJson()) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid request',
            ]);
        }

        $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);
        $member = $request->member_id;
        $member = Member::find($member);
        $invoice = $member->invoices;
        $invoiceRoute = route('form.pay.store', $invoice->token);
        $view = view('admin.ajax.member.upload-bukti-transfer', compact('member', 'invoiceRoute'))->render();

        return new JsonResponse([
            'success' => true,
            'status' => 'success',
            'view' => $view,
            'code' => 200,
        ], 200);
    }

    public function getMemberInfo(Request $request, $memberId)
    {
        if (!$request->expectsJson()) {
            return new JsonResponse([
                'success' => false,
                'status' => 'error',
                'message' => 'Invalid request',
                'code' => 400,
            ]);
        }

        if ($request->type == 'trash') {
            $member = MemberTrash::find($memberId);
        } else {
            $member = Member::find($memberId);
        }

        if (!$member) {
            return new JsonResponse([
                'success' => false,
                'status' => 'error',
                'message' => 'Member not found',
                'code' => 404,
            ]);
        }

        $view = view('admin.ajax.member.member-info', compact('member'))->render();

        return new JsonResponse([
            'success' => true,
            'status' => 'success',
            'view' => $view,
        ]);
    }

    public function getTransactionLinkTotal(Request $request, $linkId)
    {
        if (!$request->expectsJson()) {
            return new JsonResponse([
                'success' => false,
                'status' => 'error',
                'message' => 'Invalid request',
                'code' => 400,
            ]);
        }

        $link = Link::find($linkId);
        if (!$link) {
            return new JsonResponse([
                'success' => false,
                'status' => 'error',
                'message' => 'Link not found',
                'code' => 404,
            ]);
        }

        if ($link->link_type === 'free') {
            return new JsonResponse([
                'success' => true,
                'status' => 'success',
                'total' => makeCurrency(0, true),
            ]);
        }

        $orderDetail = OrderDetail::where('orderable_id', $linkId)->where('orderable_type', 'MorphLinks')->whereHas('order', function ($query) {
            $query->where('status', 'completed');
        })->get();
        $total = 0;

        foreach ($orderDetail as $order) {
            $total += $order->total;
        }

        return new JsonResponse([
            'success' => true,
            'status' => 'success',
            'total' => makeCurrency($total, true),
        ]);
    }
}
