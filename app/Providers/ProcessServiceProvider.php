<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use REBELinBLUE\Deployer\Jobs\QueueDeployment\StepsBuilder;
use REBELinBLUE\Deployer\Repositories\Contracts\DeployStepRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerLogRepositoryInterface;
use REBELinBLUE\Deployer\Services\Scripts\Parser;
use REBELinBLUE\Deployer\Services\Scripts\Runner;
use Symfony\Component\Process\Process;

/**
 * Provides Symfony process runner.
 */
class ProcessServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(StepsBuilder::class, function (Application $app) {
            $repository = $app->make(DeployStepRepositoryInterface::class);
            $log = $app->make(ServerLogRepositoryInterface::class);

            return new StepsBuilder($repository, $log);
        });

        $this->app->bind(Parser::class, function (Application $app) {
            return new Parser($app->make('files'));
        });

        $this->app->bind(Runner::class, function (Application $app) {
            $process = new Process('');
            $process->setTimeout(null);

            $logger = $app->make('log');
            $parser = $app->make(Parser::class);

            return new Runner($parser, $process, $logger);
        });
    }
}
