<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\ServiceProvider;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as Whoops;

class WhoopsServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app->make('config');

        // Only register if debugging is enabled and it is installed, i.e. on dev
        if (!$config->get('app.debug', false) || !class_exists(Whoops::class, true)) {
            return;
        }

        $this->app->bind(Whoops::class, function () {
            $whoops = new Whoops();

            /** @var \Illuminate\Http\Request $request */
            $request = $this->app->make('request');
            if ($request->expectsJson()) {
                $whoops->pushHandler(new JsonResponseHandler());
            } else {
                $whoops->pushHandler(new PrettyPageHandler());
            }

            return $whoops;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Whoops::class];
    }
}
