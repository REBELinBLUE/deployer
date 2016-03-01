<?php

namespace REBELinBLUE\Deployer\Http;

use REBELinBLUE\Deployer\Bootstrap\ConfigureLogging;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * Kernel class.
 */
class Kernel extends HttpKernel
{
    /**
     * The custom bootstrappers like Logging or Environment detector.
     * @var array
     */
    protected $customBooters = [
        \Illuminate\Foundation\Bootstrap\ConfigureLogging::class => ConfigureLogging::class,
    ];

    /**
     * Disable bootstrapper list.
     * @var array
     */
    protected $disabledBooters = [

    ];

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \REBELinBLUE\Deployer\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \REBELinBLUE\Deployer\Http\Middleware\VerifyCsrfToken::class,
            \Clockwork\Support\Laravel\ClockworkMiddleware::class,
            \GrahamCampbell\HTMLMin\Http\Middleware\MinifyMiddleware::class,
        ],

        'api' => [
            'throttle:60,1',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \REBELinBLUE\Deployer\Http\Middleware\Authenticate::class,
        'jwt' => \REBELinBLUE\Deployer\Http\Middleware\VerifyWebSocketToken::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest'  => \REBELinBLUE\Deployer\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
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
