<?php

namespace REBELinBLUE\Deployer\Http;

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
        \Illuminate\Foundation\Bootstrap\ConfigureLogging::class 
            => \REBELinBLUE\Deployer\Bootstrap\ConfigureLogging::class,
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
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \REBELinBLUE\Deployer\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \REBELinBLUE\Deployer\Http\Middleware\VerifyCsrfToken::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \REBELinBLUE\Deployer\Http\Middleware\Authenticate::class,
        //'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest'  => \REBELinBLUE\Deployer\Http\Middleware\RedirectIfAuthenticated::class,
        'minify' => \GrahamCampbell\HTMLMin\Http\Middleware\MinifyMiddleware::class,
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
