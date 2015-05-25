<?php namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

/**
 * The route service provider
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        parent::boot($router);

        $router->pattern('id', '[0-9]+');
        $router->pattern('step', '(clone|install|activate|purge)');

        $router->model('commands', 'App\Command');
        $router->model('deployments', 'App\Deployment');
        $router->model('notifications', 'App\Notification');
        $router->model('heartbeats', 'App\Heartbeat');
        $router->model('projects', 'App\Project');
        $router->model('servers', 'App\Server');
        $router->model('log', 'App\ServerLog');
        $router->model('users', 'App\User');
        $router->model('shared-files', 'App\SharedFile');
        $router->model('project-file', 'App\ProjectFile');
        $router->model('notify-email', 'App\NotifyEmail');
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $router->group(['namespace' => $this->namespace], function ($router) {
            require app_path('Http/routes.php');
        });
    }
}
