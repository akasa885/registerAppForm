<?php
use Illuminate\Support\Facades\Route;

Route::get('/linkstorage', function () {Artisan::call('storage:link');});
Route::get('/optimize', function () {Artisan::call('optimize');});
Route::get('/route-cache', function () {Artisan::call('route:cache');});
Route::get('/config-cache', function () {Artisan::call('config:cache');});