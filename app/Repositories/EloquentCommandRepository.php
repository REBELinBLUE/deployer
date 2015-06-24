<?php

namespace App\Repositories;

use App\Command;
use App\Repositories\Contracts\CommandRepositoryInterface;
use App\Repositories\EloquentRepository;

/**
 * The command repository.
 */
class EloquentCommandRepository extends EloquentRepository implements CommandRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param  Command                    $model
     * @return CommandRepositoryInterface
     */
    public function __construct(Command $model)
    {
        $this->model = $model;
    }

    /**
     * Creates a new instance of the command.
     *
     * @param  array $fields
     * @return Model
     */
    public function create(array $fields)
    {
        // Get the current highest command order
        $max = $this->model->where('project_id', $fields['project_id'])
                           ->where('step', $fields['step'])
                           ->orderBy('order', 'DESC')
                           ->first();

        $order = 0;
        if (isset($max)) {
            $order = $max->order + 1;
        }

        $fields['order'] = $order;

        $servers = null;
        if (isset($fields['servers'])) {
            $servers = $fields['servers'];
            unset($fields['servers']);
        }

        $model = $this->model->create($fields);

        if ($servers) {
            $model->servers()->sync($servers);
        }

        $model->servers; // Triggers the loading

        return $model;
    }

    /**
     * Updates an instance by it's ID.
     *
     * @param  array $fields
     * @param  int   $model_id
     * @return Model
     */
    public function updateById(array $fields, $model_id)
    {
        $model = $this->getById($model_id);

        $servers = null;
        if (isset($fields['servers'])) {
            $servers = $fields['servers'];
            unset($fields['servers']);
        }

        $model->update($fields);

        if ($servers) {
            $model->servers()->sync($servers);
        }

        $model->servers; // Triggers the loading

        return $model;
    }

    /**
     * Get's the commands in a specific step.
     *
     * @param  int        $project_id
     * @param  int        $step
     * @return Collection
     */
    public function getForDeployStep($project_id, $step)
    {
        return $this->model->where('project_id', $project_id)
                           ->with('servers')
                           ->whereIn('step', [$step - 1, $step + 1])
                           ->orderBy('order')
                           ->get();
    }
}
