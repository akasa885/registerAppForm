<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the 'lang' parameter exists in the request
        if ($request->has('lang')) {
            $available_locales = config('app.available_locales');
            $locale = $request->input('lang');
            if (in_array($locale, $available_locales)) {
                Session::put('locale', $locale);
            }
        }
        // If 'lang' is not present, check if a locale is set in the session
        elseif (!Session::has('locale')) {
            $available_locales = config('app.available_locales');
            // Extract the language preferences from the 'Accept-Language' header
            $userLanguages = $request->getLanguages();

            // Match the user's preferred languages with the available locales
            foreach ($userLanguages as $userLang) {
                if (in_array($userLang, $available_locales)) {
                    Session::put('locale', $userLang);
                    break;
                }
            }
        }

        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
