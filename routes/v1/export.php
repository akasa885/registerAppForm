<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportController;

Route::get('/member-export/{link}', [ExportController::class, 'memberExport'])->name('member-export');
Route::get('/attendance-export/{attendance}', [ExportController::class, 'attendanceExport'])->name('attendance-export');
Route::get('/member-export', [ExportController::class, 'memberExportAll'])->name('member-export-all');