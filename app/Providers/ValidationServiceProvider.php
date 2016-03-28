<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\ServiceProvider;
use REBELinBLUE\Deployer\Http\Validators\ChannelValidator;
use REBELinBLUE\Deployer\Http\Validators\HostValidator;
use REBELinBLUE\Deployer\Http\Validators\RepositoryValidator;
use REBELinBLUE\Deployer\Http\Validators\SSHKeyValidator;

/**
 * Service provider to register the validation classes.
 **/
class ValidationServiceProvider extends ServiceProvider
{
    public $validators = [
        'channel'    => ChannelValidator::class,
        'repository' => RepositoryValidator::class,
        'sshkey'     => SSHKeyValidator::class,
        'host'       => HostValidator::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->validators as $field => $validator) {
            $this->app->validator->extend($field, $validator . '@validate');
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
