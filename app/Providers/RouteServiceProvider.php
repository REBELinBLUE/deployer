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
     *
     * @return void
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
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        // Authentication screen
        Route::group([
            'middleware' => 'web',
            'namespace'  => $this->namespace,
        ], function ($router) {
            require base_path('routes/auth.php');
        });

        // Logged in routes
        Route::group([
            'middleware' => ['web', 'auth', 'jwt'],
            'namespace'  => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
            require base_path('routes/admin.php');
        });
    }

    /**
     * Define the "webhook" routes for the application.
     *
     * @return void
     */
    protected function mapHookRoutes()
    {
        Route::group([
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/hooks.php');
        });
    }
}
