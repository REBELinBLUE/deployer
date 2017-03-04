<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerLogRepositoryInterface;
use REBELinBLUE\Deployer\ServerLog;

/**
 * Clears any stalled deployments so that new deployments can be queued.
 */
class ClearStalledDeployment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployer:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancels any stalled deployments so new deployments can be run';

    /**
     * @var ServerLogRepositoryInterface
     */
    private $logRepository;

    /**
     * @var DeploymentRepositoryInterface
     */
    private $deploymentRepository;

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * ClearStalledDeployment constructor.
     *
     * @param ServerLogRepositoryInterface  $logRepository
     * @param DeploymentRepositoryInterface $deploymentRepository
     * @param ProjectRepositoryInterface    $projectRepository
     */
    public function __construct(
        ServerLogRepositoryInterface $logRepository,
        DeploymentRepositoryInterface $deploymentRepository,
        ProjectRepositoryInterface $projectRepository
    ) {
        parent::__construct();

        $this->logRepository        = $logRepository;
        $this->deploymentRepository = $deploymentRepository;
        $this->projectRepository    = $projectRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bring_back_up = false;

        // Check the app is offline, if not ask the user if it can be brought down
        if (!$this->laravel->isDownForMaintenance()) {
            $this->error(
                'You must switch to maintenance mode before running this command, ' .
                'this will ensure that no new deployments are started'
            );

            if (!$this->confirm(
                'Switch to maintenance mode now? The app will switch ' .
                'back to live mode once cleanup is finished'
            )) {
                return -1;
            }

            $bring_back_up = true;

            $this->call('down');
        }

        $this->cleanupDeployments();

        // If we prompted the user to bring the app down, bring it back up
        if ($bring_back_up) {
            $this->call('up');
        }
    }

    /**
     * Cleans up any stalled deployments in the database.
     */
    public function cleanupDeployments()
    {
        // Mark any pending steps as cancelled
        $this->logRepository->updateStatusAll(ServerLog::PENDING, ServerLog::CANCELLED);

        // Mark any running steps as failed
        $this->logRepository->updateStatusAll(ServerLog::RUNNING, ServerLog::FAILED);

        // Mark any running/pending deployments as failed
        $this->deploymentRepository->updateStatusAll(Deployment::DEPLOYING, Deployment::FAILED);
        $this->deploymentRepository->updateStatusAll(Deployment::PENDING, Deployment::FAILED);

        // Mark any aborting deployments as aborted
        $this->deploymentRepository->updateStatusAll(Deployment::ABORTING, Deployment::ABORTED);

        // Mark any deploying/pending projects as failed
        $this->projectRepository->updateStatusAll(Project::DEPLOYING, Project::FAILED);
        $this->projectRepository->updateStatusAll(Project::PENDING, Project::FAILED);
    }
}
