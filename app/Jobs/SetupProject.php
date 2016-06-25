<?php

namespace REBELinBLUE\Deployer\Jobs;

use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\ProjectFile;
use REBELinBLUE\Deployer\SharedFile;
use REBELinBLUE\Deployer\Variable;

/**
 * A class to handle cloning the command templates for the project.
 */
class SetupProject extends Job
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var int
     */
    private $template_id;

    /**
     * SetupProject constructor.
     *
     * @param Project $project
     * @param int $template_id
     */
    public function __construct(Project $project, $template_id)
    {
        $this->project     = $project;
        $this->template_id = $template_id;
    }

    /**
     * Execute the command.
     */
    public function handle()
    {
        $template = Project::findOrFail($this->template_id);

        foreach ($template->commands as $command) {
            $data               = $command->toArray();
            $data['project_id'] = $this->project->id;

            Command::create($data);
        }

        foreach ($template->variables as $variable) {
            $data               = $variable->toArray();
            $data['project_id'] = $variable->project->id;

            Variable::create($data);
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
