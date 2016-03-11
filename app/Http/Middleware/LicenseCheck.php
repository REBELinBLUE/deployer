<?php

namespace REBELinBLUE\Deployer\Http\Middleware;

use Closure;

/**
 * License check middleware.
 */
class LicenseCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->root() === 'http://deploy.aardvarklondon.com' && !$request->is('expired') && date('m-d') === '04-01') {
            return redirect('/expired');
        }

        return $next($request);
    }
}
