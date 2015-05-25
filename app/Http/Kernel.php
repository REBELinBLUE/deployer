<?php namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * Kernel class
 */
class Kernel extends HttpKernel
{

    /**
     * The custom bootstrappers like Logging or Environment detector
     * @var array
     */
    protected $customBooters = [
        'App\Bootstrap\ConfigureLogging',
    ];

    /**
     * Disable bootstrapper list
     * @var array
     */
    protected $disabledBooters = [
        'Illuminate\Foundation\Bootstrap\ConfigureLogging',
    ];

    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
        'Illuminate\Cookie\Middleware\EncryptCookies',
        'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
        'Illuminate\Session\Middleware\StartSession',
        'Illuminate\View\Middleware\ShareErrorsFromSession',
        'App\Http\Middleware\VerifyCsrfToken',
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => 'App\Http\Middleware\Authenticate',
        'guest' => 'App\Http\Middleware\RedirectIfAuthenticated',
    ];

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        return array_merge(
            array_diff(
                $this->bootstrappers,
                $this->disabledBooters
            ),
            $this->customBooters
        );
    }
}
