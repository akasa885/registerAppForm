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

Route::prefix('form')->name('form.')->group(function(){
    Route::get('/{link}', [LinkController::class, 'page'])->name('link.view');
    Route::post('/{link}', [FormController::class, 'storeIdentity'])->name('link.store');
    Route::get('/{link}/{payment}', [FormController::class, 'paymentUp'])->name('link.pay');
    Route::post('/bukti/{payment}', [FormController::class, 'payStore'])->name('pay.store');
});

Route::middleware('auth')->group(function(){
    // < ------------------------------- Artisan Route Start ----------------------------------------- >
    Route::get('/linkstorage', function () {Artisan::call('storage:link');});
    Route::get('/optimize', function () {Artisan::call('optimize');});
    Route::get('/route-cache', function () {Artisan::call('route:cache');});
    Route::get('/config-cache', function () {Artisan::call('config:cache');});
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

        // < ------------------------------- Users Admin ----------------------------------------- >
        Route::prefix('users-setting')->name('users.')->group(function(){
            Route::get('/list', [UserController::class, 'index'])->name('view');
            Route::get('/add', [UserController::class, 'create'])->name('create');
            Route::post('/add', [UserController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [UserController::class, 'show'])->name('edit');
            Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
            Route::delete('/deactive/{id}', [UserController::class, 'deactive'])->name('deactive');
            Route::put('/active/{id}', [UserController::class, 'active'])->name('active');
            Route::get('/user-view', [UserController::class, 'dtUser'])->name('dtb_list');
        });
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

