<?php

namespace REBELinBLUE\Deployer\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use GrahamCampbell\HTMLMin\HTMLMinServiceProvider;
use GrahamCampbell\HTMLMin\Http\Middleware\MinifyMiddleware;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Laracademy\Commands\MakeServiceProvider;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Token\TokenGenerator;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
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
            HTMLMinServiceProvider::class,
        ],
        'local' => [ // FIXME: Move these to dev only dependencies
            IdeHelperServiceProvider::class,
            MakeServiceProvider::class,
        ],
    ];

    /**
     * Additional web middleware to register for the environment.
     *
     * @var array
     */
    private $middleware = [
        'production' => [
            MinifyMiddleware::class,
        ],
        'local' => [

        ],
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Set up the morph map for the polymorphic relationships
        Relation::morphMap([
            'project'  => Project::class,
            'template' => Template::class,
        ]);

        Paginator::useBootstrapThree();
    }

    /**
     * Register service providers and middleware dependent upon the environment.
     */
    public function register()
    {
        $env = 'production';
        if ($this->app->environment('local', 'testing')) {
            $env = 'local';
        }

        $this->registerAdditionalProviders($this->providers[$env]);
        $this->registerAdditionalMiddleware($this->middleware[$env]);
        $this->registerDependencies();
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

    /**
     * Registers the dependencies and replace built in ones with extended classes.
     */
    private function registerDependencies()
    {
        $this->app->bind(TokenGeneratorInterface::class, TokenGenerator::class);

        $this->app->singleton('files', function () {
            return new Filesystem();
        });
    }
}
