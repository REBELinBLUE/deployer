<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

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
        \REBELinBLUE\Deployer\Events\UserWasCreated::class => [
            \REBELinBLUE\Deployer\Listeners\Events\SendSignupEmail::class,
        ],
        \REBELinBLUE\Deployer\Events\DeployFinished::class => [
            \REBELinBLUE\Deployer\Listeners\Events\NotifyDeploy::class,
        ],
        \REBELinBLUE\Deployer\Events\HeartbeatMissed::class => [
            \REBELinBLUE\Deployer\Listeners\Events\NotifyHeartbeat::class,
        ],
        \REBELinBLUE\Deployer\Events\HeartbeatRecovered::class => [
            \REBELinBLUE\Deployer\Listeners\Events\NotifyHeartbeat::class,
        ],
        \REBELinBLUE\Deployer\Events\EmailChangeRequested::class => [
            \REBELinBLUE\Deployer\Listeners\Events\EmailChangeConfirmation::class,
        ],
        \REBELinBLUE\Deployer\Events\JsonWebTokenExpired::class => [
           \REBELinBLUE\Deployer\Listeners\Events\CreateJwt::class,
        ],
        \Illuminate\Auth\Events\Login::class => [
            \REBELinBLUE\Deployer\Listeners\Events\CreateJwt::class,
        ],
        \Illuminate\Auth\Events\Logout::class => [
            \REBELinBLUE\Deployer\Listeners\Events\ClearJwt::class,
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
    }
}
