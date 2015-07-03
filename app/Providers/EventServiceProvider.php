<?php

namespace App\Providers;

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
        'App\Events\UserWasCreated'       => [
            'App\Listeners\Events\SendSignupEmail',
        ],
        'App\Events\DeployFinished'       => [
            'App\Listeners\Events\Notify',
        ],
        'App\Events\EmailChangeRequested' => [
            'App\Listeners\Events\EmailChangeConfirmation',
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
