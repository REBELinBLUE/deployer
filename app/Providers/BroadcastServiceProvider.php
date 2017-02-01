<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;
use REBELinBLUE\Deployer\User;

/**
 * The broadcast service provider.
 */
class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Broadcast::routes();

        /*
         * Authenticate the user's personal channel...
         */
        Broadcast::channel('App.User.{userId}', function (User $user, $userId) {
            return (int) $user->id === (int) $userId;
        });
    }
}
