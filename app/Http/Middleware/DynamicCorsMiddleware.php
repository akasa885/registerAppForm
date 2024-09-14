<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DynamicCorsMiddleware
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
        // Handle the response
        $response = $next($request);

        // Set CORS headers
        if (config('app.env') === 'local') {
            return $response;
        }
        
        $origin = $request->headers->get('Origin');
        if ($this->isAllowedOrigin($origin)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        $packageType = $this->getPackageType($request);
        $allowedMethods = $this->getAllowedMethods($packageType);

        $response->headers->set('Access-Control-Allow-Methods', $allowedMethods);

        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }

    /**
     * Determine allowed methods based on route.
     *
     * @param string|null $packageType
     * @return string
     */
    protected function getAllowedMethods($packageType)
    {
        // Define allowed methods based on route names
        // get, set like admins.* or api.*
        $allowedMethods = [
            'admins.*' => 'GET, POST, PUT, DELETE',
            'api.*' => 'GET, POST, PUT, DELETE',
            'users.*' => 'GET, POST, PUT, DELETE',
        ];

        foreach ($allowedMethods as $pattern => $methods) {
            if (fnmatch($pattern, $packageType)) {
                return $methods;
            }
        }

        return 'GET, POST, PUT';

    }

    /**
     * Determine the package type from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function getPackageType(Request $request)
    {
        // Example: Extract package type from route name or custom attribute
        // Assuming route names are prefixed with package type, e.g., 'admins.manage.users.show'
        $routeName = $request->route()->getName();
        return $routeName;
    }

    /**
     * Check if the origin is allowed.
     *
     * @param string|null $origin
     * @return bool
     */
    protected function isAllowedOrigin($origin)
    {
        if (!$origin) {
            return false;
        }

        $parsedUrl = parse_url($origin);
        $host = $parsedUrl['host'] ?? '';

        return preg_match('/\.?upquality\.net$/', $host);
    }
}
