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
        // Get the current highest server order
        $max = Command::where('project_id', $fields['project_id'])
                      ->where('step', $fields['step'])
                      ->orderBy('order', 'DESC')
                      ->first();

        $order = 0;
        if (isset($max)) {
            $order = $max->order + 1;
        }

        $fields['order'] = $order;

        return $this->model->create($fields);
    }
}
