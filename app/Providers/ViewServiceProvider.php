<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Contracts\View\Factory as ViewFactory;
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
        ActiveUserComposer::class => ['app'],
        HeaderComposer::class     => ['app'],
        ThemeComposer::class      => ['layout'],
        VersionComposer::class    => ['app'],
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
