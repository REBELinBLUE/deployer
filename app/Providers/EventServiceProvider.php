<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use REBELinBLUE\Deployer\Events\DeploymentFinished;
use REBELinBLUE\Deployer\Events\EmailChangeRequested;
use REBELinBLUE\Deployer\Events\HeartbeatMissed;
use REBELinBLUE\Deployer\Events\HeartbeatRecovered;
use REBELinBLUE\Deployer\Events\JsonWebTokenExpired;
use REBELinBLUE\Deployer\Events\UrlDown;
use REBELinBLUE\Deployer\Events\UrlUp;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Listeners\ClearJwt;
use REBELinBLUE\Deployer\Listeners\CreateJwt;
use REBELinBLUE\Deployer\Listeners\SendCheckUrlNotification;
use REBELinBLUE\Deployer\Listeners\SendDeploymentNotifications;
use REBELinBLUE\Deployer\Listeners\SendEmailChangeConfirmation;
use REBELinBLUE\Deployer\Listeners\SendHeartbeatNotification;
use REBELinBLUE\Deployer\Listeners\SendSignupEmail;
use REBELinBLUE\Deployer\Listeners\TestProjectUrls;

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
        DeploymentFinished::class   => [SendDeploymentNotifications::class, TestProjectUrls::class],
        HeartbeatMissed::class      => [SendHeartbeatNotification::class],
        HeartbeatRecovered::class   => [SendHeartbeatNotification::class],
        UrlUp::class                => [SendCheckUrlNotification::class],
        UrlDown::class              => [SendCheckUrlNotification::class],
        EmailChangeRequested::class => [SendEmailChangeConfirmation::class],
        JsonWebTokenExpired::class  => [CreateJwt::class],
        Login::class                => [CreateJwt::class],
        Logout::class               => [ClearJwt::class],
    ];
}
