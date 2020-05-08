<?php

namespace REBELinBLUE\Deployer\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;

/**
 * IsAdmin Middleware.
 */
class IsAdmin
{
    /**
     * @var AuthFactory
     */
    private $auth;

    /**
     * @param AuthFactory $auth
     */
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // If user is authenticated ...
        if ($this->auth->user()) {
            // ... and IS an application admin
            if ($this->auth->user()->isAdmin() === true) {
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
