<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

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
        $this->pattern('id', '[0-9]+');
        $this->pattern('step', '(clone|install|activate|purge)');

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
     */
    protected function mapWebRoutes()
    {
        // Authentication screen
        $this->middleware('web')->namespace($this->namespace)->group(base_path('routes/auth.php'));

        // Logged in routes
        $this->middleware(['web', 'auth', 'jwt'])->namespace($this->namespace)->group(base_path('routes/web.php'));

        // Admin routes
        $this->middleware(['web', 'auth', 'jwt'])->namespace($this->namespace)->group(base_path('routes/admin.php'));

        // Packages
        $this->middleware(['web', 'auth', 'jwt'])->group(base_path('routes/packages.php'));
    }

    /**
     * Define the "webhook" routes for the application.
     */
    protected function mapHookRoutes()
    {
        $this->namespace($this->namespace)->group(base_path('routes/hooks.php'));
    }
}
