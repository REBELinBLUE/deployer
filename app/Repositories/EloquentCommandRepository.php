<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Repositories\Contracts\CommandRepositoryInterface;

/**
 * The command repository.
 */
class EloquentCommandRepository extends EloquentRepository implements CommandRepositoryInterface
{
    /**
     * EloquentCommandRepository constructor.
     *
     * @param Command $model
     */
    public function __construct(Command $model)
    {
        $this->model = $model;
    }

    /**
     * Creates a new instance of the model.
     *
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields)
    {
        // Get the current highest command order
        $max = $this->model->where('target_type', $fields['target_type'])
                           ->where('target_id', $fields['target_id'])
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
     * @param array $fields
     * @param int   $model_id
     *
     * @return \Illuminate\Database\Eloquent\Model
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
     * Gets the commands for a deployment step.
     *
     * @param int    $target_id
     * @param string $target
     * @param int    $step
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForDeployStep($target_id, $target, $step)
    {
        return $this->model->where('target_type', $target)
                           ->where('target_id', $target_id)
                           ->with('servers')
                           ->whereIn('step', [$step - 1, $step + 1])
                           ->orderBy('order')
                           ->get();
    }
}
