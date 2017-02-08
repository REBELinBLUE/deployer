<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Database\Eloquent\Model;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;
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
     * @param int     $template_id
     */
    public function __construct(Project $project, $template_id)
    {
        $this->project     = $project;
        $this->template_id = $template_id;
    }

    /**
     * Execute the command.
     * @param TemplateRepositoryInterface $repository
     */
    public function handle(TemplateRepositoryInterface $repository)
    {
        $template = $repository->getById($this->template_id);

        $template->commands->each(function (Command $command) {
            $data = $this->getFieldsArray($command);

            $this->project->commands()->create($data);
        });

        $template->variables->each(function (Variable $variable) {
            $data = $this->getFieldsArray($variable);

            $this->project->variables()->create($data);
        });

        $template->sharedFiles->each(function (SharedFile $file) {
            $data = $this->getFieldsArray($file);

            $this->project->sharedFiles()->create($data);
        });

        $template->configFiles->each(function (ConfigFile $file) {
            $data = $this->getFieldsArray($file);

            $this->project->configFiles()->create($data);
        });
    }

    /**
     * Filter out the fields which aren't needed.
     *
     * @param Model $model
     *
     * @return array
     */
    private function getFieldsArray(Model $model)
    {
        return array_except($model->toArray(), ['target_type', 'target_id']);
    }
}
