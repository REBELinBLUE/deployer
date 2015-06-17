<?php

namespace App\Jobs;

use App\Command;
use App\Jobs\Job;
use App\Project;
use Illuminate\Contracts\Bus\SelfHandling;

/**
 * A class to handle cloning the command templates for the project.
 */
class SetupProject extends Job implements SelfHandling
{
    private $project;
    private $template_id;

    /**
     * Create a new command instance.
     *
     * @param Project $project
     * @param int $template_id
     * @return SetupProject
     */
    public function __construct(Project $project, $template_id)
    {
        $this->project     = $project;
        $this->template_id = $template_id;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $template = Project::findOrFail($this->template_id);

        foreach ($template->commands as $command)
        {
            $data               = $command->toArray();
            $data['project_id'] = $this->project->id;

            Command::create($data);
        }
    }
}
