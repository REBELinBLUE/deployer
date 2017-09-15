<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\ServerRepositoryInterface;
use REBELinBLUE\Deployer\Server;

/**
 * The server repository.
 */
class EloquentServerRepository extends EloquentRepository implements ServerRepositoryInterface
{
    /**
     * EloquentServerRepository constructor.
     *
     * @param Server $model
     */
    public function __construct(Server $model)
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
     * Creates a new instance of the model.
     *
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields)
    {
        // Get the current highest server order
        $max = $this->model->where('project_id', $fields['project_id'])
                           ->orderBy('order', 'DESC')
                           ->first();

        $order = 0;
        if (isset($max)) {
            $order = $max->order + 1;
        }

        $fields['type']  = Server::TYPE_UNIQUE;
        $fields['order'] = $order;

        $add_commands = false;
        if (isset($fields['add_commands'])) {
            $add_commands = $fields['add_commands'];
            unset($fields['add_commands']);
        }

        $server = $this->model->create($fields);

        // Add the server to the existing commands
        if ($add_commands) {
            foreach ($server->project->commands as $command) {
                $command->servers()->attach($server->id);
            }
        }

        return $server;
    }
}
