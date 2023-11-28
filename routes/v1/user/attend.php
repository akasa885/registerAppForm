<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AttendanceController;
Route::get('/{link}', [AttendanceController::class, 'page'])->name('link');
Route::post('/{attendance}', [AttendanceController::class, 'attending']);
Route::get('/attending/payment/waiting/{attendance}/{orderNumber}', [AttendanceController::class, 'waitingPayment'])->name('waiting-payment');