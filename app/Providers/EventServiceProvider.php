<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use REBELinBLUE\Deployer\Events\DeployFinished;
use REBELinBLUE\Deployer\Events\EmailChangeRequested;
use REBELinBLUE\Deployer\Events\HeartbeatMissed;
use REBELinBLUE\Deployer\Events\HeartbeatRecovered;
use REBELinBLUE\Deployer\Events\JsonWebTokenExpired;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Listeners\Events\ClearJwt;
use REBELinBLUE\Deployer\Listeners\Events\CreateJwt;
use REBELinBLUE\Deployer\Listeners\Events\EmailChangeConfirmation;
use REBELinBLUE\Deployer\Listeners\Events\NotifyDeploy;
use REBELinBLUE\Deployer\Listeners\Events\NotifyHeartbeat;
use REBELinBLUE\Deployer\Listeners\Events\SendSignupEmail;

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
        UserWasCreated::class       => [SendSignupEmail::class],
        DeployFinished::class       => [NotifyDeploy::class],
        HeartbeatMissed::class      => [NotifyHeartbeat::class],
        HeartbeatRecovered::class   => [NotifyHeartbeat::class],
        EmailChangeRequested::class => [EmailChangeConfirmation::class],
        JsonWebTokenExpired::class  => [CreateJwt::class],
        Login::class                => [CreateJwt::class],
        Logout::class               => [ClearJwt::class],
    ];

    /**
     * Register any other events for your application.
     */
    public function boot()
    {
        parent::boot();
    }
}
