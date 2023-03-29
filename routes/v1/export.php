<?php
use Illuminate\Support\Facades\Route;
use app\Http\Controllers\ExportController;

Route::get('/member-export/{link}', [ExportController::class, 'memberExport'])->name('member-export');