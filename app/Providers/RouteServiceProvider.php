<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use REBELinBLUE\Deployer\ServerLog;

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
     *
     * @param Router $router
     */
    public function boot(Router $router)
    {
        $router->pattern('id', '[0-9]+');
        $router->pattern('step', '(clone|install|activate|purge)');

        $router->model('log', ServerLog::class);

        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param Router $router
     */
    public function map(Router $router)
    {
        $router->group(['namespace' => $this->namespace], function ($router) {
            foreach (glob(app_path('Http/Routes') . '/*.php') as $file) {
                require $file;
            }
        });
    }
}
