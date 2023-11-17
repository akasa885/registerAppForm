<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\UrlAccessLocalCheck;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // check if current url is local or not
        UrlAccessLocalCheck::isLocal();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
