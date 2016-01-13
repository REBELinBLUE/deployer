<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\ServiceProvider;
use REBELinBLUE\Deployer\Composers\ActiveUserComposer;
use REBELinBLUE\Deployer\Composers\HeaderComposer;
use REBELinBLUE\Deployer\Composers\NavigationComposer;
use REBELinBLUE\Deployer\Composers\ThemeComposer;

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
        $factory->composer(['_partials.nav', 'dialogs.command', 'user.profile'], ActiveUserComposer::class);
        $factory->composer('_partials.nav', HeaderComposer::class);
        $factory->composer('_partials.sidebar', NavigationComposer::class);
        $factory->composer(['layout', 'user.profile'], ThemeComposer::class);
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
