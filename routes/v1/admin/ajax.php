<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AjaxController;

Route::post('/ck-upload-image', [AjaxController::class, 'ckUploadImage'])->name('ck-upload-image');
Route::get('/sub-member/particapant', [AjaxController::class, 'getSubMemberParticapant'])->name('sub-member.particapant');