<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AjaxController;

Route::post('/ck-upload-image', [AjaxController::class, 'ckUploadImage'])->name('ck-upload-image');
Route::get('/sub-member/particapant', [AjaxController::class, 'getSubMemberParticapant'])->name('sub-member.particapant');
Route::get('/member/upload/bukti-transfer', [AjaxController::class, 'getMemberUploadBuktiTransfer'])->name('member.upload.bukti-transfer');
Route::get('/member/info/{memberId}', [AjaxController::class, 'getMemberInfo'])->name('member.info');
Route::get('/transaction/link/{linkId}/total', [AjaxController::class, 'getTransactionLinkTotal'])->name('transaction.link.total');