<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * The route service provider.
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'REBELinBLUE\Deployer\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot()
    {
        Route::pattern('id', '[0-9]+');
        Route::pattern('step', '(clone|install|activate|purge)');

        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map()
    {
        $this->mapHookRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function mapWebRoutes()
    {
        // Authentication screen
        Route::middleware('web')
              ->namespace($this->namespace)
              ->group(base_path('routes/auth.php'));

        // Logged in routes
        Route::middleware(['web', 'auth', 'jwt'])
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));

        // Admin routes
        Route::middleware(['web', 'auth', 'jwt'])
             ->namespace($this->namespace)
             ->group(base_path('routes/admin.php'));
    }

    /**
     * Define the "webhook" routes for the application.
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function mapHookRoutes()
    {
        Route::middleware([])
             ->namespace($this->namespace)
             ->group(base_path('routes/hooks.php'));
    }
}
