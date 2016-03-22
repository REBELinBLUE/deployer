<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\ServiceProvider;
use REBELinBLUE\Deployer\Repositories\Contracts\CheckUrlRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\CommandRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\HeartbeatRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\NotificationRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\NotifyEmailRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectFileRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\SharedFileRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\VariableRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentCheckUrlRepository;
use REBELinBLUE\Deployer\Repositories\EloquentCommandRepository;
use REBELinBLUE\Deployer\Repositories\EloquentDeploymentRepository;
use REBELinBLUE\Deployer\Repositories\EloquentGroupRepository;
use REBELinBLUE\Deployer\Repositories\EloquentHeartbeatRepository;
use REBELinBLUE\Deployer\Repositories\EloquentNotificationRepository;
use REBELinBLUE\Deployer\Repositories\EloquentNotifyEmailRepository;
use REBELinBLUE\Deployer\Repositories\EloquentProjectFileRepository;
use REBELinBLUE\Deployer\Repositories\EloquentProjectRepository;
use REBELinBLUE\Deployer\Repositories\EloquentServerRepository;
use REBELinBLUE\Deployer\Repositories\EloquentSharedFileRepository;
use REBELinBLUE\Deployer\Repositories\EloquentTemplateRepository;
use REBELinBLUE\Deployer\Repositories\EloquentUserRepository;
use REBELinBLUE\Deployer\Repositories\EloquentVariableRepository;

/**
 * The repository service provider, binds interfaces to
 * concrete classes for dependency injection.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Binds a repository interface to an eloquent repository.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CheckUrlRepositoryInterface::class, EloquentCheckUrlRepository::class);
        $this->app->bind(CommandRepositoryInterface::class, EloquentCommandRepository::class);
        $this->app->bind(DeploymentRepositoryInterface::class, EloquentDeploymentRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, EloquentGroupRepository::class);
        $this->app->bind(HeartbeatRepositoryInterface::class, EloquentHeartbeatRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, EloquentNotificationRepository::class);
        $this->app->bind(NotifyEmailRepositoryInterface::class, EloquentNotifyEmailRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, EloquentProjectRepository::class);
        $this->app->bind(ProjectFileRepositoryInterface::class, EloquentProjectFileRepository::class);
        $this->app->bind(ServerRepositoryInterface::class, EloquentServerRepository::class);
        $this->app->bind(SharedFileRepositoryInterface::class, EloquentSharedFileRepository::class);
        $this->app->bind(TemplateRepositoryInterface::class, EloquentTemplateRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(VariableRepositoryInterface::class, EloquentVariableRepository::class);
    }
}
