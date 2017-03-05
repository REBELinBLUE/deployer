<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Events\DeploymentFinished;
use REBELinBLUE\Deployer\Events\EmailChangeRequested;
use REBELinBLUE\Deployer\Events\HeartbeatMissed;
use REBELinBLUE\Deployer\Events\HeartbeatRecovered;
use REBELinBLUE\Deployer\Events\JsonWebTokenExpired;
use REBELinBLUE\Deployer\Events\Listeners\ClearJwt;
use REBELinBLUE\Deployer\Events\Listeners\CreateJwt;
use REBELinBLUE\Deployer\Events\Listeners\SendCheckUrlNotifications;
use REBELinBLUE\Deployer\Events\Listeners\SendDeploymentNotifications;
use REBELinBLUE\Deployer\Events\Listeners\SendEmailChangeConfirmation;
use REBELinBLUE\Deployer\Events\Listeners\SendHeartbeatNotifications;
use REBELinBLUE\Deployer\Events\Listeners\SendSignupEmail;
use REBELinBLUE\Deployer\Events\Listeners\TestProjectUrls;
use REBELinBLUE\Deployer\Events\Observers\ChannelObserver;
use REBELinBLUE\Deployer\Events\Observers\CheckUrlObserver;
use REBELinBLUE\Deployer\Events\Observers\HeartbeatObserver;
use REBELinBLUE\Deployer\Events\Observers\ProjectObserver;
use REBELinBLUE\Deployer\Events\Observers\ServerLogObserver;
use REBELinBLUE\Deployer\Events\UrlDown;
use REBELinBLUE\Deployer\Events\UrlUp;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\ServerLog;

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
        DeploymentFinished::class   => [SendDeploymentNotifications::class, TestProjectUrls::class],
        EmailChangeRequested::class => [SendEmailChangeConfirmation::class],
        HeartbeatMissed::class      => [SendHeartbeatNotifications::class],
        HeartbeatRecovered::class   => [SendHeartbeatNotifications::class],
        JsonWebTokenExpired::class  => [CreateJwt::class],
        Login::class                => [CreateJwt::class],
        Logout::class               => [ClearJwt::class],
        UrlDown::class              => [SendCheckUrlNotifications::class],
        UrlUp::class                => [SendCheckUrlNotifications::class],
        UserWasCreated::class       => [SendSignupEmail::class],
    ];

    /**
     * Register the application's event listeners.
     */
    public function boot()
    {
        parent::boot();

        Channel::observe(ChannelObserver::class);
        CheckUrl::observe(CheckUrlObserver::class);
        Heartbeat::observe(HeartbeatObserver::class);
        Project::observe(ProjectObserver::class);
        ServerLog::observe(ServerLogObserver::class);
    }
}
