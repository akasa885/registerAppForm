<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

Route::get('/edit', [UserController::class, 'edit'])->name('edit');
Route::put('/update', [UserController::class, 'updateMineProfile'])->name('update');
Route::get('/change-password', [UserController::class, 'changePassword'])->name('change-password');
Route::put('/update-password', [UserController::class, 'updatePassword'])->name('update-password');