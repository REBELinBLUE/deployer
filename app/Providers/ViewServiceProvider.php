<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\ServiceProvider;
use REBELinBLUE\Deployer\Http\Composers\HeaderComposer;
use REBELinBLUE\Deployer\Http\Composers\NavigationComposer;
use REBELinBLUE\Deployer\Http\Composers\ThemeComposer;

/**
 * The view service provider.
 */
class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param  \Illuminate\Contracts\View\Factory $factory
     * @return void
     */
    public function boot(Factory $factory)
    {
        $factory->composer(['layout', 'user.profile'], ThemeComposer::class);
        $factory->composer('_partials.nav', HeaderComposer::class);
        $factory->composer('_partials.sidebar', NavigationComposer::class);
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
}
