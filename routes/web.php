<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\IndexController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [IndexController::class, 'index'])->name('home');

Route::prefix('form')->name('form.')->group(__DIR__ . '/v1/user/form.php');

Route::middleware('auth')->group(function(){
    // < ------------------------------- Artisan Route Start ----------------------------------------- >
    Route::name('artisan')->group(__DIR__ . '/artisan/artisan.php');
    // < ------------------------------- Artisan Route End ----------------------------------------- >    

    Route::group(['prefix' => 'laravel-filemanager'], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });
});

Route::prefix('dpanel')->name('admin.')->group(function(){
    Route::middleware('auth')->group(function(){
        Route::get('/', function () {
            return redirect(route('admin.dashboard'));
        });
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // < ------------------------------- Link Control ----------------------------------------- >
        Route::prefix('link')->name('link.')->group(function(){
            Route::get('/', [LinkController::class, 'index'])->name('view');
            Route::get('/add-event-payment', [LinkController::class, 'createPay'])->name('create');
            Route::get('/add-event-free', [LinkController::class, 'createFree'])->name('create.free');
            Route::post('/store', [LinkController::class, 'store'])->name('store');
            Route::get('/members/{id}', [LinkController::class, 'show'])->name('detail');
            Route::get('/edit-event-payment/{id}', [LinkController::class, 'edit'])->name('edit');
            Route::get('/edit-event-free/{id}', [LinkController::class, 'editFree'])->name('edit.free');
            Route::post('/update/{id}', [LinkController::class, 'update'])->name('update');
            Route::get('/dtable', [LinkController::class, 'dtb_link'])->name('dtable');
            Route::get('/dtable-member/{id}', [LinkController::class, 'dtb_memberLink'])->name('dtable.member');
        });
        // < ------------------------------- Link Control ----------------------------------------- >
        
        // < ------------------------------- Attendance Control ----------------------------------------- >
        Route::prefix('attendance')->name('attendance.')->group(__DIR__.'/v1/admin/attendance.php');
        // < ------------------------------- Attendance Control ----------------------------------------- >

        // < ------------------------------- Users Admin ----------------------------------------- >
        Route::prefix('users-setting')->name('users.')->group(__DIR__.'/v1/admin/setting_user.php');
        // < ------------------------------- Users Admin ----------------------------------------- >

        // < ------------------------------- Members Admin ----------------------------------------- >
        Route::prefix('member')->name('member.')->group(function(){
            Route::get('/pay-sheet/{id}', [MemberController::class, 'viewSheet'])->name('lihat.bukti');
            Route::post('/accepted-receipt', [MemberController::class, 'updateBukti'])->name('up.bukti');
        });
        // < ------------------------------- Members Admin ----------------------------------------- >

    });

    Auth::routes();
});

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

