<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportController;

Route::get('/member-export/{link}', [ExportController::class, 'memberExport'])->name('member-export');