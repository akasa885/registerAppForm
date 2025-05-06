<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\FormController;

Route::get('/{link}', [LinkController::class, 'page'])->name('link.view');
Route::post('/{link}', [FormController::class, 'storeIdentity'])->name('link.store');
Route::post('/member-only/{link}', [FormController::class, 'storeMemberOnly'])->name('link.member-only.store');
Route::post('/link/{link}/multi-registrant', [FormController::class, 'storeMultiRegistrant'])->name('link.multi-registrant.store');
Route::get('/{link}/{payment}', [FormController::class, 'paymentUp'])->name('link.pay');
Route::post('/bukti/{payment}', [FormController::class, 'payStore'])->name('pay.store');
Route::post('/renew/payment/link', [FormController::class, 'requestNewPayment'])->name('pay.renew');