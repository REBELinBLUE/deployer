<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\ServiceProvider;
use REBELinBLUE\Deployer\Repositories\Contracts\ChannelRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\CheckUrlRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\CommandRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ConfigFileRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\DeployStepRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\HeartbeatRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\NotificationRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\RefRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerLogRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\SharedFileRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\VariableRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentChannelRepository;
use REBELinBLUE\Deployer\Repositories\EloquentCheckUrlRepository;
use REBELinBLUE\Deployer\Repositories\EloquentCommandRepository;
use REBELinBLUE\Deployer\Repositories\EloquentConfigFileRepository;
use REBELinBLUE\Deployer\Repositories\EloquentDeploymentRepository;
use REBELinBLUE\Deployer\Repositories\EloquentDeployStepRepository;
use REBELinBLUE\Deployer\Repositories\EloquentGroupRepository;
use REBELinBLUE\Deployer\Repositories\EloquentHeartbeatRepository;
use REBELinBLUE\Deployer\Repositories\EloquentNotificationRepository;
use REBELinBLUE\Deployer\Repositories\EloquentProjectRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRefRepository;
use REBELinBLUE\Deployer\Repositories\EloquentServerLogRepository;
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
    protected $repositories = [
        ChannelRepositoryInterface::class        => EloquentChannelRepository::class,
        CheckUrlRepositoryInterface::class       => EloquentCheckUrlRepository::class,
        CommandRepositoryInterface::class        => EloquentCommandRepository::class,
        ConfigFileRepositoryInterface::class     => EloquentConfigFileRepository::class,
        DeploymentRepositoryInterface::class     => EloquentDeploymentRepository::class,
        DeployStepRepositoryInterface::class     => EloquentDeployStepRepository::class,
        GroupRepositoryInterface::class          => EloquentGroupRepository::class,
        HeartbeatRepositoryInterface::class      => EloquentHeartbeatRepository::class,
        NotificationRepositoryInterface::class   => EloquentNotificationRepository::class,
        ProjectRepositoryInterface::class        => EloquentProjectRepository::class,
        RefRepositoryInterface::class            => EloquentRefRepository::class,
        ServerLogRepositoryInterface::class      => EloquentServerLogRepository::class,
        ServerRepositoryInterface::class         => EloquentServerRepository::class,
        SharedFileRepositoryInterface::class     => EloquentSharedFileRepository::class,
        TemplateRepositoryInterface::class       => EloquentTemplateRepository::class,
        UserRepositoryInterface::class           => EloquentUserRepository::class,
        VariableRepositoryInterface::class       => EloquentVariableRepository::class,
    ];

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
