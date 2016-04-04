<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * The application service provider.
 */
class AppServiceProvider extends ServiceProvider
{
    public $live_providers = [
        'GrahamCampbell\HTMLMin\HTMLMinServiceProvider',
    ];

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
        $providers = ($this->app->environment('local') ? $this->local_providers : $this->live_providers);

        foreach ($providers as $provider) {
            if (class_exists($provider, true)) {
                $this->app->register($provider);
            }
        }
    }
}
