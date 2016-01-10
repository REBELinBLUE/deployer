<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * The application service provider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Define a constant for the application version
        define('APP_VERSION', trim(file_get_contents(app_path('../VERSION'))));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
