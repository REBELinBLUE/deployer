<?php namespace App\Commands;

use Auth;
use Queue;
use App\Project;
use App\Deployment;
use App\DeployStep;
use App\ServerLog;
use App\Commands\Command;
use App\Commands\DeployProject;
use Illuminate\Contracts\Bus\SelfHandling;

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
        $this->deployment->commit = Deployment::LOADING;
        $this->deployment->save();

        $this->deployment->project->status = Project::PENDING;
        $this->deployment->project->save();

        $hooks = [
            'Clone'     => null,
            'Install'   => null,
            'Activate'  => null,
            'Purge'     => null
        ];

        foreach ($this->project->commands as $command) {
            $steps  = explode(' ', $command->step);
            $action = $steps[1];
            $when   = $steps[0];

            if (!is_array($hooks[$action])) {
                $hooks[$action] = [];
            }

            if (!isset($hooks[$action][$when])) {
                $hooks[$action][$when] = [];
            }

            $hooks[$action][$when][] = $command;
        }

        // FIXME: Refactor this, lots of repeating code!
        foreach (array_keys($hooks) as $command) {
            if (isset($hooks[$command]['Before'])) {
                foreach ($hooks[$command]['Before'] as $hook) {
                    $step = new DeployStep;
                    $step->stage = 'Before ' . $command;
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
            $step->stage = $command;
            $step->deployment_id = $this->deployment->id;
            $step->save();

            foreach ($this->project->servers as $server) {
                $log = new ServerLog;
                $log->server_id = $server->id;
                $log->deploy_step_id = $step->id;
                $log->save();
            }

            if (isset($hooks[$command]['After'])) {
                foreach ($hooks[$command]['After'] as $hook) {
                    $step = new DeployStep;
                    $step->stage = 'After ' . $command;
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
