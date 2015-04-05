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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Project $project, Deployment $deployment)
    {
        $this->project = $project;
        $this->deployment = $deployment;
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

            if (!is_array($hooks[$action])) {
                $hooks[$action] = [];
            }

            if (!isset($hooks[$action][$when])) {
                $hooks[$action][$when] = [];
            }

            $hooks[$action][$when][] = $command;
        }

        // FIXME: Refactor this, lots of repeating code!
        foreach (array_keys($hooks) as $stage) {
            if (isset($hooks[$stage]['before'])) {
                foreach ($hooks[$stage]['before'] as $hook) {
                    $step = new DeployStep;
                    $step->stage = (int) $stage - 1;
                    $step->command_id = $hook->id;
                    $step->deployment_id = $this->deployment->id;
                    $step->save();

                    foreach ($this->project->servers as $server) {
                        $log = new ServerLog;
                        $log->server_id = $server->id;
                        $log->deploy_step_id = $step->id;
                        $log->save();
                    }
                }
            }

            $step = new DeployStep;
            $step->stage = $stage;
            $step->deployment_id = $this->deployment->id;
            $step->save();

            foreach ($this->project->servers as $server) {
                $log = new ServerLog;
                $log->server_id = $server->id;
                $log->deploy_step_id = $step->id;
                $log->save();
            }

            if (isset($hooks[$stage]['after'])) {
                foreach ($hooks[$stage]['after'] as $hook) {
                    $step = new DeployStep;
                    $step->stage = (int) $stage + 1;
                    $step->command_id = $hook->id;
                    $step->deployment_id = $this->deployment->id;
                    $step->save();

                    foreach ($this->project->servers as $server) {
                        $log = new ServerLog;
                        $log->server_id = $server->id;
                        $log->deploy_step_id = $step->id;
                        $log->save();
                    }
                }
            }
        }

        Queue::pushOn('deploy', new DeployProject($this->deployment));
    }
}
