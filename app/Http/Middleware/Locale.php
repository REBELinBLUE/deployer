<?php

namespace REBELinBLUE\Deployer\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Sets the applications locale based on the user's language.
 */
class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->language) {
            resolve('locale')->setLocale($user->language);
        }

        return $next($request);
    }
}
