<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SiteSettingController;

Route::get('/', [SiteSettingController::class, 'site'])->name('view');
Route::post('/update', [SiteSettingController::class, 'siteUpdate'])->name('update');
Route::get('/midtrans', [SiteSettingController::class, 'midtrans'])->name('midtrans');
Route::post('/midtrans', [SiteSettingController::class, 'midtransUpdate'])->name('midtrans.update');