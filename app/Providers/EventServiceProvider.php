<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * The event service provider.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'REBELinBLUE\Deployer\Events\UserWasCreated'       => [
            'REBELinBLUE\Deployer\Listeners\Events\SendSignupEmail',
        ],
        'REBELinBLUE\Deployer\Events\DeployFinished'       => [
            'REBELinBLUE\Deployer\Listeners\Events\Notify',
        ],
        'REBELinBLUE\Deployer\Events\EmailChangeRequested' => [
            'REBELinBLUE\Deployer\Listeners\Events\EmailChangeConfirmation',
        ],
        'Illuminate\Auth\Events\Login' => [
            'REBELinBLUE\Deployer\Listeners\Events\CreateJwt',
        ],
        'Illuminate\Auth\Events\Logout' => [
            'REBELinBLUE\Deployer\Listeners\Events\ClearJwt',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
