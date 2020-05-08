<?php

namespace REBELinBLUE\Deployer\Jobs\QueueDeployment;

use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\DeployStepRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerLogRepositoryInterface;

/**
 * Generates the DeployStep and ServerLog instances for each command.
 */
class StepsBuilder
{
    /**
     * @var DeployStepRepositoryInterface
     */
    private $repository;

    /**
     * @var ServerLogRepositoryInterface
     */
    private $log;

    /**
     * @var Project
     */
    private $project;

    /**
     * @param DeployStepRepositoryInterface $repository
     * @param ServerLogRepositoryInterface  $log
     */
    public function __construct(DeployStepRepositoryInterface $repository, ServerLogRepositoryInterface $log)
    {
        $this->repository = $repository;
        $this->log        = $log;
    }

    /**
     * Takes the grouped commands and creates instances of the required models.
     *
     * @param Collection $grouped
     * @param Project    $project
     * @param int        $deployment
     * @param array      $optional
     */
    public function build(Collection $grouped, Project $project, int $deployment, array $optional = []): void
    {
        $this->project = $project;

        $grouped->each(function ($commands, int $step) use ($deployment, $optional) {
            $this->createCustomSteps($commands->get('before'), $step - 1, $deployment, $optional);
            $this->createDeployStep($step, $deployment);
            $this->createCustomSteps($commands->get('after'), $step + 1, $deployment, $optional);
        });
    }

    /**
     * Loops through the commands for a specific stage and creates instances.
     *
     * @param Collection $commands
     * @param int        $step
     * @param int        $deployment
     * @param array      $optional
     */
    private function createCustomSteps(Collection $commands, int $step, int $deployment, array $optional = []): void
    {
        $commands->filter(function (Command $command) use ($optional) {
            return $this->shouldIncludeCommand($command, $optional);
        })->each(function (Command $command) use ($step, $deployment) {
            $this->createCommandStep($step, $command, $deployment);
        });
    }

    /**
     * Determines whether a given command should be included based on whether it is included in the array of
     * optional commands to include.
     *
     * @param Command $command
     * @param array   $optional
     *
     * @return bool
     */
    private function shouldIncludeCommand(Command $command, array $optional = []): bool
    {
        return !$command->optional || ($command->optional && in_array($command->id, $optional, true));
    }

    /**
     * Create an instance of DeployStep and a ServerLog entry for each server assigned to the command.
     * Command steps are the user created commands which run before and after the built in steps.
     *
     * @param int     $stage
     * @param Command $command
     * @param int     $deployment
     */
    private function createCommandStep(int $stage, Command $command, $deployment): void
    {
        $step = $this->repository->create([
            'stage'         => $stage,
            'deployment_id' => $deployment,
            'command_id'    => $command->id,
        ]);

        $command->servers->each(function ($server) use ($step) {
            $this->log->create([
                'server_id'      => $server->id,
                'deploy_step_id' => $step->id,
            ]);
        });
    }

    /**
     * Create an instance of DeployStep and a ServerLog entry for each server which can have code deployed.
     * Deploy steps are the built in 4 steps, clone, install, activate, purge.
     *
     * @param int $stage
     * @param int $deployment
     */
    private function createDeployStep(int $stage, int $deployment): void
    {
        $step = $this->repository->create([
            'stage'         => $stage,
            'deployment_id' => $deployment,
        ]);

        $this->project->servers->filter(function ($server) {
            return $server->deploy_code;
        })->each(function ($server) use ($step) {
            $this->log->create([
                'server_id'      => $server->id,
                'deploy_step_id' => $step->id,
            ]);
        });
    }
}
