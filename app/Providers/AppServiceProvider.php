<?php

namespace REBELinBLUE\Deployer\Providers;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Application;
use Illuminate\Notifications\Channels\SlackWebhookChannel;
use Illuminate\Support\ServiceProvider;
use MicheleAngioni\MultiLanguage\LanguageManager;
use NotificationChannels\Webhook\WebhookChannel;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Template;
use function GuzzleHttp\default_user_agent;

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
        if ($this->app->environment() === 'local') {
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
     * Registers the Language Manager and guzzle.
     */
    private function registerDependencies()
    {
        $this->app->singleton('locale', function (Application $app) {
            return $app->make(LanguageManager::class);
        });

        $this->registerGuzzleClientOptions();
    }

    /**
     * Registers the guzzle client with options defined in config.
     */
    private function registerGuzzleClientOptions()
    {
        $this->app->alias(HttpClient::class, 'HttpClient');
        $this->app->when(SlackWebhookChannel::class)->needs(HttpClient::class)->give('HttpClient');

        $this->app->bind('HttpClient', function (Application $app, array $additional = []) {
            $config = array_merge($app->make('config')->get('deployer.guzzle') ?: [], [
                'headers' => ['User-Agent' => 'Deployer/' . APP_VERSION . ' ' . default_user_agent()],
            ]);

            return new HttpClient(array_merge_recursive($config, $additional));
        });

        // Inject the Guzzle client for the Webhook channel so that we can set some defaults
        $this->app->when(WebhookChannel::class)
                  ->needs(HttpClient::class)
                  ->give(function (Application $app) {
                      return $app->make('HttpClient', [
                          'headers' => ['Content-Type' => 'application/json'],
                      ]);
                  });
    }
}
