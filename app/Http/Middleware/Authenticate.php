<?php

namespace REBELinBLUE\Deployer\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

/**
 * Authentication middleware.
 */
class Authenticate
{
    /**
     * @var Redirector
     */
    private $redirector;

    /**
     * @var ResponseFactory
     */
    private $response;

    /**
     * @param Redirector      $redirector
     * @param ResponseFactory $response
     */
    public function __construct(Redirector $redirector, ResponseFactory $response)
    {
        $this->redirector = $redirector;
        $this->response   = $response;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure                  $next
     * @param string|null              $guard
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax()) {
                return $this->response->make('Unauthorized.', 401);
            }

            return $this->redirector->guest('login');
        }

        return $next($request);
    }
}
