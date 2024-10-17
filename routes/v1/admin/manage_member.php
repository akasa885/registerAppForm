<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MemberController;

Route::get('/', function(){
    return redirect()->route('admin.member.list.member');
});

Route::get('/list', [MemberController::class, 'index'])->name('list.member');
Route::get('/log/event/{member}', [MemberController::class, 'logEvent'])->name('log.event');

Route::get('/table/data', [MemberController::class, 'dtListAll'])->name('dt.data.member');

Route::get('/pay-sheet/{id}', [MemberController::class, 'viewSheet'])->name('lihat.bukti');
Route::post('/accepted-receipt', [MemberController::class, 'updateBukti'])->name('up.bukti');
Route::delete('/delete-registrant/{link}/id-member/{member}', [MemberController::class, 'deleteRegistrant'])->name('delete.registrant');
