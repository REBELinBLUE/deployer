<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\ServiceProvider;
use REBELinBLUE\Deployer\Composers\ActiveUserComposer;
use REBELinBLUE\Deployer\Composers\HeaderComposer;
use REBELinBLUE\Deployer\Composers\NavigationComposer;
use REBELinBLUE\Deployer\Composers\ThemeComposer;
use REBELinBLUE\Deployer\Composers\VersionComposer;

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
        // FIXME make an array which we loop through
        $factory->composer(['_partials.nav', 'dialogs.command', 'user.profile'], ActiveUserComposer::class);
        $factory->composer('_partials.nav', HeaderComposer::class);
        $factory->composer('_partials.sidebar', NavigationComposer::class);
        $factory->composer(['layout', 'user.profile'], ThemeComposer::class);
        $factory->composer('_partials.update', VersionComposer::class);
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
