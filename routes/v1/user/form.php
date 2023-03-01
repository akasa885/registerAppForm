<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\FormController;

Route::get('/{link}', [LinkController::class, 'page'])->name('link.view');
Route::post('/{link}', [FormController::class, 'storeIdentity'])->name('link.store');
Route::get('/{link}/{payment}', [FormController::class, 'paymentUp'])->name('link.pay');
Route::post('/bukti/{payment}', [FormController::class, 'payStore'])->name('pay.store');