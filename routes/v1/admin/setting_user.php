<?php
use Illuminate\Support\Facades\Route;

Route::get('/list', [UserController::class, 'index'])->name('view');
Route::get('/add', [UserController::class, 'create'])->name('create');
Route::post('/add', [UserController::class, 'store'])->name('store');
Route::get('/edit/{id}', [UserController::class, 'show'])->name('edit');
Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
Route::delete('/deactive/{id}', [UserController::class, 'deactive'])->name('deactive');
Route::put('/active/{id}', [UserController::class, 'active'])->name('active');
Route::get('/user-view', [UserController::class, 'dtUser'])->name('dtb_list');