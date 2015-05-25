<?php namespace App\Commands;

use Auth;
use Queue;
use App\Command as Stage;
use App\Project;
use App\Deployment;
use App\DeployStep;
use App\ServerLog;
use App\Commands\Command;
use App\Commands\DeployProject;
use Illuminate\Contracts\Bus\SelfHandling;

/**
 * Generates the required database entries to queue a deployment
 */
class QueueDeployment extends Command implements SelfHandling
{
    private $project;
    private $deployment;
    private $optional;

    /**
     * Create a new command instance.
     *
     * @param Project $project
     * @param Deployment $deployment
     * @return QueueDeployment
     */
    public function __construct(Project $project, Deployment $deployment, array $optional = array())
    {
        $this->project = $project;
        $this->deployment = $deployment;
        $this->optional = $optional;
    }

    /**
     * Execute the command.
     *
     * @return void
     * TODO: refactor
     */
    public function handle()
    {
        $this->deployment->status = Deployment::PENDING;
        $this->deployment->started_at = date('Y-m-d H:i:s');
        $this->deployment->project_id = $this->project->id;

        if (Auth::check()) {
            $this->deployment->user_id = Auth::user()->id;
        }

        $this->deployment->committer = Deployment::LOADING;
        $this->deployment->commit    = Deployment::LOADING;
        $this->deployment->save();

        $this->deployment->project->status = Project::PENDING;
        $this->deployment->project->save();


        $hooks = [
            Stage::DO_CLONE    => null,
            Stage::DO_INSTALL  => null,
            Stage::DO_ACTIVATE => null,
            Stage::DO_PURGE    => null
        ];

        foreach ($this->project->commands as $command) {
            $action = $command->step - 1;
            $when = ($command->step % 3 === 0 ? 'after' : 'before');
            if ($when === 'before') {
                $action = $command->step + 1;
            }

            // Check if the command is optional, and if it is check it exists in the optional array
            if ($command->optional && !in_array($command->id, $this->optional)) {
                continue;
            }

            if (!is_array($hooks[$action])) {
                $hooks[$action] = [];
            }

            if (!isset($hooks[$action][$when])) {
                $hooks[$action][$when] = [];
            }

            $hooks[$action][$when][] = $command;
        }

        foreach (array_keys($hooks) as $stage) {
            $before = $stage - 1;
            $after = $stage + 1;

            if (isset($hooks[$stage]['before'])) {
                foreach ($hooks[$stage]['before'] as $hook) {
                    $this->createCommandStep($before, $hook);
                }
            }

            $this->createDeployStep($stage);

            if (isset($hooks[$stage]['after'])) {
                foreach ($hooks[$stage]['after'] as $hook) {
                    $this->createCommandStep($after, $hook);
                }
            }
        }

        Queue::pushOn('deploy', new DeployProject($this->deployment));
    }

    /**
     * Create an instance of DeployStep and a ServerLog entry for each server assigned to the command
     *
     * @param int $stage
     * @param Command $command
     * @return void
     * TODO: refactor these 2 functions
     */
    private function createCommandStep($stage, Stage $command)
    {
        $step = DeployStep::create([
            'stage'         => $stage,
            'deployment_id' => $this->deployment->id,
            'command_id'    => $command->id
        ]);

        foreach ($command->servers as $server) {
            ServerLog::create([
                'server_id'      => $server->id,
                'deploy_step_id' => $step->id
            ]);
        }
    }

    /**
     * Create an instance of DeployStep and a ServerLog entry for each server which can have code deployed
     *
     * @param int $stage
     * @return void
     */
    private function createDeployStep($stage)
    {
        $step = DeployStep::create([
            'stage'         => $stage,
            'deployment_id' => $this->deployment->id
        ]);

        foreach ($this->project->servers as $server) {
            // If command is null it is preparing one of the 4 default steps so
            // skip servers which shouldn't have the code deployed
            if (!$server->deploy_code) {
                continue;
            }

            ServerLog::create([
                'server_id'      => $server->id,
                'deploy_step_id' => $step->id
            ]);
        }
    }
}
