<?php

namespace REBELinBLUE\Deployer\Jobs\QueueDeployment;

use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\DeployStepRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerLogRepositoryInterface;

/**
 * Generates the DeployStep and ServerLog instances for each command.
 */
class CommandCreator
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
     * @var Deployment
     */
    private $deployment;

    public function __construct(DeployStepRepositoryInterface $repository, ServerLogRepositoryInterface $log)
    {
        $this->repository = $repository;
        $this->log        = $log;
    }

    public function build(array $hooks, Project $project, Deployment $deployment, array $optional = [])
    {
        $this->project    = $project;
        $this->deployment = $deployment;

        foreach (array_keys($hooks) as $stage) {
            $before = $stage - 1;
            $after  = $stage + 1;

            if (isset($hooks[$stage]['before'])) {
                foreach ($hooks[$stage]['before'] as $command) {
                    if ($command->optional && !in_array($command->id, $optional, true)) {
                        continue;
                    }

                    $this->createCommandStep($before, $command);
                }
            }

            $this->createDeployStep($stage);

            if (isset($hooks[$stage]['after'])) {
                foreach ($hooks[$stage]['after'] as $command) {
                    if ($command->optional && !in_array($command->id, $optional, true)) {
                        continue;
                    }

                    $this->createCommandStep($after, $command);
                }
            }
        }
    }

    /**
     * Create an instance of DeployStep and a ServerLog entry for each server assigned to the command.
     *
     * @param int   $stage
     * @param Stage $command
     */
    private function createCommandStep($stage, Command $command)
    {
        $step = $this->repository->create([
            'stage'         => $stage,
            'deployment_id' => $this->deployment->id,
            'command_id'    => $command->id,
        ]);

        foreach ($command->servers as $server) {
            $this->log->create([
                'server_id'      => $server->id,
                'deploy_step_id' => $step->id,
            ]);
        }
    }

    /**
     * Create an instance of DeployStep and a ServerLog entry for each server which can have code deployed.
     *
     * @param int $stage
     */
    private function createDeployStep($stage)
    {
        $step = $this->repository->create([
            'stage'         => $stage,
            'deployment_id' => $this->deployment->id,
        ]);

        foreach ($this->project->servers as $server) {
            // If command is null it is preparing one of the 4 default steps so
            // skip servers which shouldn't have the code deployed
            if (!$server->deploy_code) {
                continue;
            }

            $this->log->create([
                'server_id'      => $server->id,
                'deploy_step_id' => $step->id,
            ]);
        }
    }
}
