<?php

namespace REBELinBLUE\Deployer\Providers;

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
        view()->composer(
            ['layout', 'user.profile'],
            'REBELinBLUE\Deployer\Http\Composers\ThemeComposer'
        );

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
        view()->composer('_partials.nav', 'REBELinBLUE\Deployer\Http\Composers\HeaderComposer');
        view()->composer('_partials.sidebar', 'REBELinBLUE\Deployer\Http\Composers\NavigationComposer');
    }
}
