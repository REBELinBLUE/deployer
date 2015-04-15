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
     * @todo refactor
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
                    $this->createStep($before, $hook);
                }
            }

            $this->createStep($stage);

            if (isset($hooks[$stage]['after'])) {
                foreach ($hooks[$stage]['after'] as $hook) {
                    $this->createStep($after, $hook);
                }
            }
        }

        Queue::pushOn('deploy', new DeployProject($this->deployment));
    }

    /**
     * Create an instance of DeployStep and a ServerLog entry for each server
     *
     * @param int $stage
     * @param Command|null $command
     * @return void
     * @todo Only create instances of ServerLog for each server which is assigned the command
     * @todo Refactor this
     */
    private function createStep($stage, Stage $command = null)
    {
        $step = new DeployStep;
        $step->stage = $stage;

        if (!is_null($command)) {
            $step->command_id = $command->id;
        }

        $step->deployment_id = $this->deployment->id;
        $step->save();

        foreach ($this->project->servers as $server) {
            // If command is null it is preparing one of the 4 default steps so skip servers should don't have the code deployed
            if (is_null($command) && !$server->deploy_code) {
                continue;
            }

            $log = new ServerLog;
            $log->server_id = $server->id;
            $log->deploy_step_id = $step->id;
            $log->save();
        }
    }
}
