<?php

namespace REBELinBLUE\Deployer\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Clockwork\Support\Laravel\ClockworkMiddleware;
use Clockwork\Support\Laravel\ClockworkServiceProvider;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use GrahamCampbell\HTMLMin\HTMLMinServiceProvider;
use GrahamCampbell\HTMLMin\Http\Middleware\MinifyMiddleware;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use MicheleAngioni\MultiLanguage\LanguageManager;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Token\TokenGenerator;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use REBELinBLUE\Deployer\Template;
use Themsaid\Langman\LangmanServiceProvider;

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
        'local' => [
            IdeHelperServiceProvider::class,
            ClockworkServiceProvider::class,
            LangmanServiceProvider::class,
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
            ClockworkMiddleware::class,
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

        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }
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
     * Registers the Language Manager and guzzle.
     */
    private function registerDependencies()
    {
        $this->app->singleton('locale', function (Application $app) {
            return $app->make(LanguageManager::class);
        });

        $this->app->bind(TokenGeneratorInterface::class, TokenGenerator::class);

        $this->replaceBuiltinPackageDependencies();
    }

    /**
     * Replace dependencies register by laravel and packages.
     */
    private function replaceBuiltinPackageDependencies()
    {
        $this->app->singleton('files', function () {
            return new Filesystem();
        });

        $this->app->singleton(FakerGenerator::class, function () {
            return FakerFactory::create('en_GB');
        });
    }
}
