<?php

namespace App\Jobs;

use App\Commands\Command;
use App\Command;
use App\Project;
use Illuminate\Contracts\Bus\SelfHandling;

/**
 * A class to handle cloning the command templates for the project.
 */
class SetupProject extends Command implements SelfHandling
{
    private $project;
    private $template;

    /**
     * Create a new command instance.
     *
     * @param Project $project
     * @param Project $template
     * @return SetupProject
     */
    public function __construct(Project $project, Project $template)
    {
        $this->project  = $project;
        $this->template = $template;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        // FIXME: Also copy persistent files
        foreach ($this->template->commands as $command) {

            $data               = $command->toArray();
            $data['project_id'] = $this->project->id;

            Command::create($data);
        }
    }
}
