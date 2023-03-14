<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AttendanceController;
Route::get('/{link}', [AttendanceController::class, 'page'])->name('link');
Route::post('/{link}', [AttendanceController::class, 'attending']);