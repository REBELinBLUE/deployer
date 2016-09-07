<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Template;

/**
 * The application service provider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Additional service providers to register for the environment.
     *
     * @var array
     */
    private $providers = [
        'production' => [
            'GrahamCampbell\HTMLMin\HTMLMinServiceProvider',
        ],
        'local' => [
            'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider',
            'Clockwork\Support\Laravel\ClockworkServiceProvider',
            'Themsaid\Langman\LangmanServiceProvider',
        ],
    ];

    /**
     * Additional web middleware to register for the environment.
     *
     * @var array
     */
    private $middleware = [
        'production' => [
            'GrahamCampbell\HTMLMin\Http\Middleware\MinifyMiddleware',
        ],
        'local' => [
            'Clockwork\Support\Laravel\ClockworkMiddleware',
        ],
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Set up the morph map for the polymorphic relationships
        Relation::morphMap([
            'project' => Project::class,
            'template' => Template::class,
        ]);
    }

    /**
     * Register service providers and middleware dependent upon the environment.
     */
    public function register()
    {
        $env = 'production';
        if ($this->app->environment() === 'local') {
            $env = 'local';
        }

        $this->registerAdditionalProviders($this->providers[$env]);
        $this->registerAdditionalMiddleware($this->middleware[$env]);
    }

    /**
     * Register additional service providers.
     *
     * @param array $providers
     */
    private function registerAdditionalProviders(array $providers)
    {
        foreach ($providers as $provider) {
            if (class_exists($provider, true)) {
                $this->app->register($provider);
            }
        }
    }

    /**
     * Register additional middleware.
     *
     * @param array $middlewares
     */
    private function registerAdditionalMiddleware(array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            if (class_exists($middleware, true)) {
                $this->app->router->pushMiddlewareToGroup('web', $middleware);
            }
        }
    }
}
