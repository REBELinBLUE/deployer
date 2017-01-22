<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\ServiceProvider;
use REBELinBLUE\Deployer\View\Composers\ActiveUserComposer;
use REBELinBLUE\Deployer\View\Composers\HeaderComposer;
use REBELinBLUE\Deployer\View\Composers\NavigationComposer;
use REBELinBLUE\Deployer\View\Composers\ThemeComposer;
use REBELinBLUE\Deployer\View\Composers\VersionComposer;

/**
 * The view service provider.
 */
class ViewServiceProvider extends ServiceProvider
{
    public $composers = [
        ActiveUserComposer::class => ['_partials.nav', 'commands.dialog', 'user.profile', 'deployment.log'],
        HeaderComposer::class     => ['_partials.nav'],
        NavigationComposer::class => ['_partials.sidebar'],
        ThemeComposer::class      => ['layout', 'user.profile'],
        VersionComposer::class    => ['_partials.update'],
    ];

    /**
     * Bootstrap the application services.
     *
     * @param ViewFactory $factory
     */
    public function boot(ViewFactory $factory)
    {
        foreach ($this->composers as $composer => $views) {
            $factory->composer($views, $composer);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
