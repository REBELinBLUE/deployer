<?php

namespace REBELinBLUE\Deployer\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Clockwork\Support\Laravel\ClockworkMiddleware;
use Clockwork\Support\Laravel\ClockworkServiceProvider;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use GrahamCampbell\HTMLMin\HTMLMinServiceProvider;
use GrahamCampbell\HTMLMin\Http\Middleware\MinifyMiddleware;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Application;
use Illuminate\Notifications\Channels\SlackWebhookChannel;
use Illuminate\Support\ServiceProvider;
use MicheleAngioni\MultiLanguage\LanguageManager;
use NotificationChannels\Webhook\WebhookChannel;
use REBELinBLUE\Deployer\Jobs\QueueDeployment\StepsBuilder;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\DeployStepRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerLogRepositoryInterface;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Parser;
use REBELinBLUE\Deployer\Services\Scripts\Runner;
use REBELinBLUE\Deployer\Services\Token\TokenGenerator;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use REBELinBLUE\Deployer\Template;
use Symfony\Component\Process\Process;
use Themsaid\Langman\LangmanServiceProvider;
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

        $this->registerServiceDependencies();
        $this->registerGuzzleClientOptions();
        $this->replacePackageDependencies();
    }

    /**
     * Register service classes into the container.
     */
    private function registerServiceDependencies()
    {
        $this->app->bind(StepsBuilder::class, function (Application $app) {
            $repository = $app->make(DeployStepRepositoryInterface::class);
            $log = $app->make(ServerLogRepositoryInterface::class);

            return new StepsBuilder($repository, $log);
        });

        $this->app->bind(Parser::class, function (Application $app) {
            return new Parser($app->make('files'));
        });

        $this->app->bind(Runner::class, function (Application $app) {
            $process = new Process('');
            $process->setTimeout(null);

            $logger = $app->make('log');

            return new Runner($app->make(Parser::class), $process, $logger);
        });

        $this->app->bind(TokenGeneratorInterface::class, TokenGenerator::class);
    }

    /**
     * Replace dependencies register by laravel and packages.
     */
    private function replacePackageDependencies()
    {
        $this->app->singleton('files', function () {
            return new Filesystem();
        });

        $this->app->singleton(FakerGenerator::class, function () {
            return FakerFactory::create('en_GB');
        });

        $client = $this->getClient();

        // Inject the Guzzle client for the Webhook channel so that we can set some defaults
        $this->app->when(WebhookChannel::class)
            ->needs(HttpClient::class)
            ->give(function (Application $app) use ($client) {
                return $client($app, [
                    'headers' => ['Content-Type' => 'application/json'],
                ]);
            });
    }

    /**
     * Registers the guzzle client with options defined in config.
     */
    private function registerGuzzleClientOptions()
    {
        $this->app->alias('HttpClient', HttpClient::class);
        $this->app->when(SlackWebhookChannel::class)->needs(HttpClient::class)->give('HttpClient');

        $client = $this->getClient();

        $this->app->bind('HttpClient', function (Application $app) use ($client) {
            return $client($app);
        });
    }

    /**
     * Creates a closure for the Guzzle client so that additional options can be provided.
     *
     * @return \Closure
     */
    private function getClient()
    {
        return function (Application $app, array $additional = []) {
            $config = array_merge($app->make('config')->get('deployer.guzzle') ?: [], [
                'headers' => ['User-Agent' => 'Deployer/' . APP_VERSION . ' ' . default_user_agent()],
            ]);

            return new HttpClient(array_merge_recursive($config, $additional));
        };
    }
}
