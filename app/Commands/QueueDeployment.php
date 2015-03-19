<?php namespace App\Commands;

use App\Commands\Command;
use App\Commands\DeployProject;

use Illuminate\Contracts\Bus\SelfHandling;

use App\Project;
use App\Deployment;
use App\DeployStep;
use App\ServerLog;

use Queue;

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
        $this->deployment->status = 'Pending';
        $this->deployment->run = date('Y-m-d H:i:s');
        $this->deployment->project_id = $this->project->id;
        $this->deployment->user_id = 1; // FIXME: Get logged in user
        $this->deployment->committer = 'Loading'; // FIXME: Better values for these
        $this->deployment->commit = 'Loading';
        $this->deployment->save();

        $this->deployment->project->status = 'Pending';
        $this->deployment->project->save();

        // FIXME: Add entries for before/after etc
        foreach (['Clone', 'Install', 'Activate', 'Purge'] as $command) {
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
        }

        Queue::pushOn('deploy', new DeployProject($this->deployment));
    }
}
