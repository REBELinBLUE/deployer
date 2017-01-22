<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\ServiceProvider;
use REBELinBLUE\Deployer\Contracts\Repositories\ChannelRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\CheckUrlRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\CommandRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\ConfigFileRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\HeartbeatRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\NotificationRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\ServerRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\SharedFileRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\TemplateRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\UserRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\VariableRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentChannelRepository;
use REBELinBLUE\Deployer\Repositories\EloquentCheckUrlRepository;
use REBELinBLUE\Deployer\Repositories\EloquentCommandRepository;
use REBELinBLUE\Deployer\Repositories\EloquentConfigFileRepository;
use REBELinBLUE\Deployer\Repositories\EloquentDeploymentRepository;
use REBELinBLUE\Deployer\Repositories\EloquentGroupRepository;
use REBELinBLUE\Deployer\Repositories\EloquentHeartbeatRepository;
use REBELinBLUE\Deployer\Repositories\EloquentNotificationRepository;
use REBELinBLUE\Deployer\Repositories\EloquentProjectRepository;
use REBELinBLUE\Deployer\Repositories\EloquentServerRepository;
use REBELinBLUE\Deployer\Repositories\EloquentSharedFileRepository;
use REBELinBLUE\Deployer\Repositories\EloquentTemplateRepository;
use REBELinBLUE\Deployer\Repositories\EloquentUserRepository;
use REBELinBLUE\Deployer\Repositories\EloquentVariableRepository;

/**
 * The repository service provider, binds interfaces to concrete classes for dependency injection.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public $repositories = [
        ChannelRepositoryInterface::class      => EloquentChannelRepository::class,
        CheckUrlRepositoryInterface::class     => EloquentCheckUrlRepository::class,
        CommandRepositoryInterface::class      => EloquentCommandRepository::class,
        ConfigFileRepositoryInterface::class   => EloquentConfigFileRepository::class,
        DeploymentRepositoryInterface::class   => EloquentDeploymentRepository::class,
        GroupRepositoryInterface::class        => EloquentGroupRepository::class,
        HeartbeatRepositoryInterface::class    => EloquentHeartbeatRepository::class,
        NotificationRepositoryInterface::class => EloquentNotificationRepository::class,
        ProjectRepositoryInterface::class      => EloquentProjectRepository::class,
        ServerRepositoryInterface::class       => EloquentServerRepository::class,
        SharedFileRepositoryInterface::class   => EloquentSharedFileRepository::class,
        TemplateRepositoryInterface::class     => EloquentTemplateRepository::class,
        UserRepositoryInterface::class         => EloquentUserRepository::class,
        VariableRepositoryInterface::class     => EloquentVariableRepository::class,
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //
    }

    /**
     * Bind the repository interface to the implementations.
     */
    public function register()
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }
}
