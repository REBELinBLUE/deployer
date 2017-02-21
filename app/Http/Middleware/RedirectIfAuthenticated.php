<?php

namespace REBELinBLUE\Deployer\Http\Middleware;

use Closure;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware to prevent access to pages when already authenticated.
 */
class RedirectIfAuthenticated
{
    /**
     * @var Redirector
     */
    private $redirector;

    /**
     * @param Redirector $redirector
     */
    public function __construct(Redirector $redirector)
    {
        $this->redirector = $redirector;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure                  $next
     * @param string|null              $guard
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return $this->redirector->to('/');
        }

        return $next($request);
    }
}
