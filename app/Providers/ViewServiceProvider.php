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
    public $composers = [
        ActiveUserComposer::class => ['_partials.nav', 'dialogs.command', 'user.profile'],
        HeaderComposer::class     => '_partials.nav',
        NavigationComposer::class => '_partials.sidebar',
        ThemeComposer::class      => ['layout', 'user.profile'],
        VersionComposer::class    => '_partials.update'
    ];

    /**
     * Bootstrap the application services.
     *
     * @param  \Illuminate\Contracts\View\Factory $factory
     * @return void
     */
    public function boot(Factory $factory)
    {
        foreach ($this->composers as $composer => $views) {
            $factory->composer($views, $composer);
        }
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
