<?php

namespace REBELinBLUE\Deployer\Http;

use Fideloper\Proxy\TrustProxies;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Bootstrap\ConfigureLogging;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use REBELinBLUE\Deployer\Bootstrap\ConfigureLogging as HttpLogging;
use REBELinBLUE\Deployer\Http\Middleware\Authenticate;
use REBELinBLUE\Deployer\Http\Middleware\EncryptCookies;
use REBELinBLUE\Deployer\Http\Middleware\RedirectIfAuthenticated;
use REBELinBLUE\Deployer\Http\Middleware\RefreshJsonWebToken;
use REBELinBLUE\Deployer\Http\Middleware\VerifyCsrfToken;

/**
 * Kernel class.
 */
class Kernel extends HttpKernel
{
    /**
     * The custom bootstrappers like Logging or Environment detector.
     *
     * @var array
     */
    protected $customBooters = [
        ConfigureLogging::class => HttpLogging::class,
    ];

    /**
     * Disable bootstrapper list.
     *
     * @var array
     */
    protected $disabledBooters = [

    ];

    /**
     * The application's global HTTP middleware stack.
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        CheckForMaintenanceMode::class,
        TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ],
        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'jwt'        => RefreshJsonWebToken::class,
        'auth'       => Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'bindings'   => SubstituteBindings::class,
        'can'        => Authorize::class,
        'guest'      => RedirectIfAuthenticated::class,
        'throttle'   => ThrottleRequests::class,
    ];

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        foreach ($this->bootstrappers as &$bootstrapper) {
            foreach ($this->customBooters as $sourceBooter => $newBooter) {
                if ($bootstrapper === $sourceBooter) {
                    $bootstrapper = $newBooter;
                    unset($this->customBooters[$sourceBooter]);
                }
            }
        }

        return array_merge(
            array_diff(
                $this->bootstrappers,
                $this->disabledBooters
            ),
            $this->customBooters
        );
    }
}
