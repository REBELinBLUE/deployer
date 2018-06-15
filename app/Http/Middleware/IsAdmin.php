<?php

namespace REBELinBLUE\Deployer\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If user is authenticated ...
        if (Auth::user()) {

            // ... and IS an application admin
            if (Auth::user()->isAdmin() === true) {
                // authorization granted
                return $next($request);
            }

            // ... otherwise there is nothing we can do. Aborting...
            abort(403);
        }

        // User should login
        return redirect('/');
    }
}
