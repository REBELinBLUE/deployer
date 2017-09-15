<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\SharedServerRepositoryInterface;
use REBELinBLUE\Deployer\Server;

/**
 * The shared server repository.
 */
class EloquentSharedServerRepository extends EloquentRepository implements SharedServerRepositoryInterface
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
                    ->where('type', Server::TYPE_SHARED)
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
        $fields['type'] = Server::TYPE_SHARED;

        return $this->model->create($fields);
    }
}
