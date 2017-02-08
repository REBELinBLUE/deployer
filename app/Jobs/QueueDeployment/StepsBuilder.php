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
     * @todo clean up more
     */
    public function build(Collection $grouped, Project $project, $deployment, array $optional = [])
    {
        $this->project = $project;

        foreach (array_keys($grouped) as $stage) {
            $before = $stage - 1;
            $after  = $stage + 1;

//            foreach ($grouped[$stage]['before'] as $command) {
//                if ($command->optional && !in_array($command->id, $optional, true)) {
//                    continue;
//                }
//
//                $this->createCommandStep($before, $command);
//            }

            $this->createDeployStep($stage, $deployment);

//            foreach ($grouped[$stage]['after'] as $command) {
//                if ($command->optional && !in_array($command->id, $optional, true)) {
//                    continue;
//                }
//
//                $this->createCommandStep($after, $command, $deployment);
//            }
        }
    }

    /**
     * Create an instance of DeployStep and a ServerLog entry for each server assigned to the command.
     * Command steps are the user created commands which run before and after the built in steps.
     *
     * @param int     $stage
     * @param Command $command
     * @param int     $deployment
     */
    private function createCommandStep($stage, Command $command, $deployment)
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
    private function createDeployStep($stage, $deployment)
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
