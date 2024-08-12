<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    private $unwantedHeaders = ['X-Powered-By', 'server', 'Server'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!app()->environment('local')) {
            $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $response->headers->set('Expect-CT', 'enforce, max-age=30');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

            $this->removeUnwantedHeaders($this->unwantedHeaders);
        }

        return $response;
    }

    private function removeUnwantedHeaders($headers): void
    {
        foreach ($headers as $header) {
            header_remove($header);
        }
    }
}
