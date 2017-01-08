<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

/**
 * The broadcast service provider.
 */
class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        /*
         * Authenticate the user's personal channel...
         */
        Broadcast::channel('App.User.*', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });
    }
}
