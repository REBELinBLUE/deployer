<?php

namespace REBELinBLUE\Deployer\Jobs;

use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Template;

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
        $template = Template::findOrFail($this->template_id);

        foreach ($template->commands as $command) {
            $data = $command->toArray();

            $this->project->commands()->create($data);
        }

        foreach ($template->variables as $variable) {
            $data = $variable->toArray();

            $this->project->variables()->create($data);
        }

        foreach ($template->sharedFiles as $file) {
            $data = $file->toArray();

            $this->project->sharedFiles()->create($data);
        }

        foreach ($template->projectFiles as $file) {
            $data = $file->toArray();

            $this->project->projectFiles()->create($data);
        }
    }
}
