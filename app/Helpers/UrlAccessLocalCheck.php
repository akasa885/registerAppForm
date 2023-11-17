<?php

namespace App\Helpers;

use Illuminate\Contracts\Container\BindingResolutionException;

class UrlAccessLocalCheck
{
    /**
     * Check if current url is local or not
     * @return void 
     * @throws BindingResolutionException 
     */
    public static function isLocal():void
    {
        // get current request url
        $currentUrl = url()->current();
        // check does current url is .test
        $isLocal = str_contains($currentUrl, '.test');
        // or check does current url is localhost
        $isLocal = $isLocal || str_contains($currentUrl, 'localhost');
        // if current url is not .test, then set environment to production
        $environment = $isLocal ? 'local' : 'production';
        // set environment
        app()->detectEnvironment(function () use ($environment) {
            return $environment;
        });

        // then force https if environment is production
        if ($environment === 'production') {
            app()['request']->server->set('HTTPS', true);
        }
    }
}