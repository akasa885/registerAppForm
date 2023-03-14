<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AttendanceController;

Route::get('/', [Attendancecontroller::class, 'index'])->name('view');
Route::get('/create/session/{type}', [Attendancecontroller::class, 'create'])->where(['type' => 'day|hourly'])->name('create');
Route::post('/store/session/{type}', [Attendancecontroller::class, 'store'])->where(['type' => 'day|hourly'])->name('store');
Route::delete('/delete/session/{attendance}', [Attendancecontroller::class, 'destroy'])->name('delete');
Route::get('/attending/members/{attendance}', [Attendancecontroller::class, 'show'])->name('detail');
Route::get('/dt/attendance', [Attendancecontroller::class, 'dtb_attendance'])->name('dt.attendance');
Route::get('/dtable', [Attendancecontroller::class, 'dtb_member'])->name('dtable');