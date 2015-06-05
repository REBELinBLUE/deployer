<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Http\Request;

/**
 * Middleware to prevent CSRF
 */
class VerifyCsrfToken extends BaseVerifier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->excludedRoutes($request)) {
            return $this->addCookieToResponse($request, $next($request));
        }

        return parent::handle($request, $next);
    }

    /**
     * Determines whether or not the request should be excluded from CSRF protection
     *
     * @param \Illuminate\Http\Request $request
     * @return boolean
     */
    protected function excludedRoutes(Request $request)
    {
        $routes = [
            'deploy/*'
        ];

        foreach ($routes as $route) {
            if ($request->is($route)) {
                return true;
            }
        }

        return false;
    }
}
