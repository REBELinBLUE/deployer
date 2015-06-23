<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->bindInterface('CheckUrl');
        $this->bindInterface('Command');
        $this->bindInterface('Deployment');
        $this->bindInterface('Group');
        $this->bindInterface('Heartbeat');
        $this->bindInterface('Notification');
        $this->bindInterface('NotifyEmail');
        $this->bindInterface('Project');
        $this->bindInterface('ProjectFile');
        $this->bindInterface('Server');
        $this->bindInterface('SharedFile');
        $this->bindInterface('Template');
        $this->bindInterface('User');
    }

    /**
     * Binds a repository interface to an eloquent repository.
     *
     * @param  string $name
     * @return void
     */
    public function bindInterface($name)
    {
        $this->app->bind(
            'App\\Repositories\\Contracts\\' . $name . 'RepositoryInterface',
            'App\\Repositories\\Eloquent' . $name . 'Repository'
        );
    }
}
