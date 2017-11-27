<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\ServiceProvider;
use ZxcvbnPhp\Zxcvbn;
use REBELinBLUE\Deployer\Validators\ZxcvbnValidator;

class ZxcvbnServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $validator = $this->app->make('validator');

        $validator->extend('zxcvbn', ZxcvbnValidator::class . '@validate');
        //$validator->replacer('zxcvbn', ZxcvbnValidator::class . '@messages');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('zxcvbn', function () {
            return new Zxcvbn();
        });

        $this->app->alias(Zxcvbn::class, 'zxcvbn');
    }
}
