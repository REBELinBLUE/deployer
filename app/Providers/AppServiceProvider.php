<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * The application service provider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Service providers which are only used in production.
     * @var array
     */
    public $production_providers = [
        'GrahamCampbell\HTMLMin\HTMLMinServiceProvider',
    ];

    /**
     * Service providers which are only used in development.
     * @var array
     */
    public $local_providers = [
        'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider',
        'Clockwork\Support\Laravel\ClockworkServiceProvider',
        'Themsaid\Langman\LangmanServiceProvider',
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $providers = ($this->app->environment('local') ? $this->local_providers : $this->production_providers);

        foreach ($providers as $provider) {
            if (class_exists($provider, true)) {
                $this->app->register($provider);
            }
        }
    }
}
