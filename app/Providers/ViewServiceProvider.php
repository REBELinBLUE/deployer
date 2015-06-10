<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * The view service provider.
 */
class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->composeNavigation();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Registers view composers.
     *
     * @return void
     */
    private function composeNavigation()
    {
        view()->composer('_partials.nav', 'App\Http\Composers\HeaderComposer');
        view()->composer('_partials.sidebar', 'App\Http\Composers\NavigationComposer');
    }
}
