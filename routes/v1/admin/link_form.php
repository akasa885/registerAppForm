<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LinkController;

Route::get('/', [LinkController::class, 'index'])->name('view');
Route::get('/add-event-payment', [LinkController::class, 'createPay'])->name('create');
Route::get('/add-event-free', [LinkController::class, 'createFree'])->name('create.free');
Route::post('/store', [LinkController::class, 'store'])->name('store');
Route::get('/members/{id}', [LinkController::class, 'show'])->name('detail');
Route::get('/members-trash/{link}', [LinkController::class, 'showTrash'])->name('detail.trash');
Route::get('/edit-event-payment/{id}', [LinkController::class, 'edit'])->name('edit');
Route::get('/edit-event-free/{id}', [LinkController::class, 'editFree'])->name('edit.free');
Route::put('/update/{id}', [LinkController::class, 'update'])->name('update');
Route::delete('/delete/form/{link}', [LinkController::class, 'destroy'])->name('delete');
Route::get('/dtable', [LinkController::class, 'dtb_link'])->name('dtable');
Route::get('/dtable-member/{id}', [LinkController::class, 'dtb_memberLink'])->name('dtable.member');
Route::get('/dtable-member-link-trash/{link}', [LinkController::class, 'dtb_memberLinkTrash'])->name('dtable.member.trash');
Route::post('/change/visibility/{id}', [LinkController::class, 'changeVisibility'])->name('change.visibility');