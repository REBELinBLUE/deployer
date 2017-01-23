<?php

namespace REBELinBLUE\Deployer\Providers;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use REBELinBLUE\Deployer\Contracts\Github\LatestReleaseInterface;
use REBELinBLUE\Deployer\Github\LatestRelease;

/**
 * Service provider to register the LatestRelease class as a singleton.
 **/
class UpdateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // Define a constant for the application version
        if (!defined('APP_VERSION')) {
            define('APP_VERSION', trim(file_get_contents(base_path('VERSION'))));
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(LatestReleaseInterface::class, LatestRelease::class);

        $this->app->singleton('deployer.update-check', function (Application $app) {
            $cache = $app->make('cache.store');
            $client = $app->make(Client::class);
            $token = config('deployer.github_oauth_token');

            return new LatestRelease($cache, $client, $token);
        });

        $this->app->alias('deployer.update-check', LatestReleaseInterface::class);
    }
}
