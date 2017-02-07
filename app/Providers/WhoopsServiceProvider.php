<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\ServiceProvider;
use Whoops\Handler\HandlerInterface as WhoopsHandlerInterface;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as Whoops;

/**
 * Service provider to provide the Whoops exception handle.
 */
class WhoopsServiceProvider extends ServiceProvider
{
    /**
     * Defer loading until actually needed.
     *
     * @var bool
     */
    public $defer = true;

    /**
     * Register the application services.
     */
    public function register()
    {
        if (!$this->useWhoops()) {
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
        if (!$this->useWhoops()) {
            return [];
        }

        return [Whoops::class];
    }

    /**
     * Determines whether or not whoops should be bound to the service container.
     *
     * @return bool
     */
    private function useWhoops()
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app->make('config');

        // Only register if debugging is enabled and it is installed, i.e. on dev
        if (!$config->get('app.debug', false) || !interface_exists(WhoopsHandlerInterface::class, true)) {
            return false;
        }

        return true;
    }
}
