<?php

namespace REBELinBLUE\Deployer\Jobs;

use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Jobs\Job;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\ProjectFile;
use REBELinBLUE\Deployer\SharedFile;

/**
 * A class to handle cloning the command templates for the project.
 */
class SetupProject extends Job
{
    private $project;
    private $template_id;

    /**
     * Create a new command instance.
     *
     * @param  Project      $project
     * @param  int          $template_id
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

        foreach ($template->commands as $command) {
            $data               = $command->toArray();
            $data['project_id'] = $this->project->id;

            Command::create($data);
        }

        foreach ($template->sharedFiles as $file) {
            $data               = $file->toArray();
            $data['project_id'] = $this->project->id;

            SharedFile::create($data);
        }

        foreach ($template->projectFiles as $file) {
            $data               = $file->toArray();
            $data['project_id'] = $this->project->id;

            ProjectFile::create($data);
        }
    }
}
