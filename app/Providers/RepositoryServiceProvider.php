<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        $this->app->bind(
            'App\Repositories\Contracts\DeploymentRepositoryInterface',
            'App\Repositories\EloquentDeploymentRepository'
        );

        $this->app->bind(
            'App\Repositories\Contracts\ProjectRepositoryInterface',
            'App\Repositories\EloquentProjectRepository'
        );

        $this->app->bind(
            'App\Repositories\Contracts\GroupRepositoryInterface',
            'App\Repositories\EloquentGroupRepository'
        );
    }
}
