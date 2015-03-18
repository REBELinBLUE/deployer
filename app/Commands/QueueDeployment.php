<?php namespace App\Commands;

use App\Commands\Command;
use App\Commands\DeployProject;

use Illuminate\Contracts\Bus\SelfHandling;

use App\Project;
use App\Deployment;
use App\DeployStep;

use Queue;

class QueueDeployment extends Command implements SelfHandling
{
	private $project, $deployment;

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
       // FIXME: Check if a deployment is already in progress

        $this->deployment->run = date('Y-m-d H:i:s');
        $this->deployment->project_id = $this->project->id;
        $this->deployment->user_id = 1; // FIXME: Get logged in user
        $this->deployment->committer = 'Loading'; // FIXME: Better values for these
        $this->deployment->commit = 'Loading';
        $this->deployment->save();

        $this->deployment->project->status = 'Running';
        $this->deployment->project->save();

		// FIXME: Add entries for before/after for each server?
        foreach (['Clone', 'Install', 'Activate', 'Purge'] as $command)
        {
            $step = new DeployStep;
            $step->stage = $command;
            $step->deployment_id = $this->deployment->id;
            $step->save();
        }


        Queue::pushOn('deploy', new DeployProject($this->deployment));
	}

}
