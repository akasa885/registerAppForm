<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\MemberController;
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

        Route::prefix('link')->name('link.')->group(function(){
            Route::get('/', [LinkController::class, 'index'])->name('view');
            Route::get('/add', [LinkController::class, 'create'])->name('create');
            Route::post('/store', [LinkController::class, 'store'])->name('store');
            Route::get('/members/{id}', [LinkController::class, 'show'])->name('detail');
            Route::get('/edit/{id}', [LinkController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [LinkController::class, 'update'])->name('update');
            Route::get('/dtable', [LinkController::class, 'dtb_link'])->name('dtable');
            Route::get('/dtable-member/{id}', [LinkController::class, 'dtb_memberLink'])->name('dtable.member');
        });
    });

    Auth::routes();
});

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

