<?php

namespace REBELinBLUE\Deployer\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Routing\Redirector;

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
     * @var AuthFactory
     */
    private $auth;

    /**
     * @param Redirector  $redirector
     * @param AuthFactory $auth
     */
    public function __construct(Redirector $redirector, AuthFactory $auth)
    {
        $this->redirector = $redirector;
        $this->auth       = $auth;
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
        if ($this->auth->guard($guard)->check()) {
            return $this->redirector->to('/');
        }

        return $next($request);
    }
}
