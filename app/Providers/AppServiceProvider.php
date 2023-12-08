<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\UrlAccessLocalCheck;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Helpers\Midtrans;

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
        config()->set('midtrans', Midtrans::createConfig());
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::enforceMorphMap([
            'MorphLinks' => 'App\Models\Link',
            'MorphAttend' => 'App\Models\Attendance',
        ]);

        view()->composer('partials.language_switcher_front', function ($view) {
            $view->with('current_locale', app()->getLocale());
            $view->with('available_locales', config('app.available_locales'));
        });

        view()->composer('partials.language_switcher_admin', function ($view) {
            $view->with('current_locale', app()->getLocale());
            $view->with('available_locales', config('app.available_locales'));
        });
    }
}
