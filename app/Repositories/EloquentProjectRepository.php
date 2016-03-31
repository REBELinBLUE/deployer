<?php

namespace REBELinBLUE\Deployer\Repositories;

use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Jobs\SetupProject;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;

/**
 * The project repository.
 */
class EloquentProjectRepository extends EloquentRepository implements ProjectRepositoryInterface
{
    use DispatchesJobs;

    /**
     * Class constructor.
     *
     * @param  Project                   $model
     * @return EloquentProjectRepository
     */
    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    /**
     * Gets all projects.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->model
                    ->notTemplates()
                    ->orderBy('name')
                    ->get();
    }

    /**
     * Creates a new instance of project.
     *
     * @param  array $fields
     * @return Model
     */
    public function create(array $fields)
    {
        $template = false;
        if (array_key_exists('template_id', $fields)) {
            $template = $fields['template_id'];

            unset($fields['template_id']);
        }

        if (array_key_exists('private_key', $fields) && empty($fields['private_key'])) {
            unset($fields['private_key']);
        }

        $project = $this->model->create($fields);

        if ($template) {
            $this->dispatch(new SetupProject(
                $project,
                $template
            ));
        }

        return $project;
    }

    /**
     * Update an instance of the project.
     *
     * @param  array $fields
     * @return Model
     */
    public function updateById(array $fields, $model_id)
    {
        $project = $this->getById($model_id);

        if (array_key_exists('private_key', $fields)) {
            if (empty($fields['private_key'])) {
                unset($fields['private_key']);
            } else {
                $project->public_key = '';
            }
        }

        $project->update($fields);

        return $project;
    }

    /**
     * Gets a project by it's hash.
     *
     * @param  string    $hash
     * @return Heartbeat
     */
    public function getByHash($hash)
    {
        return $this->model->where('hash', $hash)->firstOrFail();
    }
}
