<?php

namespace REBELinBLUE\Deployer\Repositories;

use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Contracts\Repositories\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Jobs\SetupProject;
use REBELinBLUE\Deployer\Project;

/**
 * The project repository.
 */
class EloquentProjectRepository extends EloquentRepository implements ProjectRepositoryInterface
{
    use DispatchesJobs;

    /**
     * EloquentProjectRepository constructor.
     *
     * @param Project $model
     */
    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->model
                    ->orderBy('name')
                    ->get();
    }

    /**
     * {@inheritdoc}
     * @dispatches SetupProject
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getByHash($hash)
    {
        return $this->model->where('hash', $hash)->firstOrFail();
    }
}
