<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AjaxController as AdminAjaxC;
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
Route::prefix('attend')->name('attend.')->group(__DIR__ . '/v1/user/attend.php');

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
        Route::prefix('link')->name('link.')->group(__DIR__.'/v1/admin/link_form.php');
        // < ------------------------------- Link Control ----------------------------------------- >
        
        // < ------------------------------- Attendance Control ----------------------------------------- >
        Route::prefix('attendance')->name('attendance.')->group(__DIR__.'/v1/admin/attendance.php');
        // < ------------------------------- Attendance Control ----------------------------------------- >

        // < ------------------------------- Users Admin ----------------------------------------- >
        Route::prefix('users-setting')->name('users.')->group(__DIR__.'/v1/admin/setting_user.php');
        // < ------------------------------- Users Admin ----------------------------------------- >

        // < -------------------------------Export ----------------------------------------- >
        Route::prefix('export')->name('export.')->group(__DIR__.'/v1/export.php');
        // < -------------------------------Export ----------------------------------------- >

        // < ------------------------------- Ajax Admin ----------------------------------------- >
        Route::prefix('ajax')->name('ajax.')->group(__DIR__.'/v1/admin/ajax.php');
        // < ------------------------------- Ajax Admin ----------------------------------------- >

        // < ------------------------------- Members Admin ----------------------------------------- >
        Route::prefix('member')->name('member.')->group(function(){
            Route::get('/pay-sheet/{id}', [MemberController::class, 'viewSheet'])->name('lihat.bukti');
            Route::post('/accepted-receipt', [MemberController::class, 'updateBukti'])->name('up.bukti');
        });
        // < ------------------------------- Members Admin ----------------------------------------- >

        // < ------------------------------- Session Ajax Check ----------------------------------------- >
        Route::post('/check-session', [AdminAjaxC::class, 'checkSession'])->name('check.session');
        // < ------------------------------- Session Ajax Check ----------------------------------------- >

    });

    Auth::routes();
});

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

