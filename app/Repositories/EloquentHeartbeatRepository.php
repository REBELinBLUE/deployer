<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Contracts\Repositories\HeartbeatRepositoryInterface;
use REBELinBLUE\Deployer\Heartbeat;

/**
 * The shared file repository.
 */
class EloquentHeartbeatRepository extends EloquentRepository implements HeartbeatRepositoryInterface
{
    /**
     * EloquentHeartbeatRepository constructor.
     *
     * @param Heartbeat $model
     */
    public function __construct(Heartbeat $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getByHash($hash)
    {
        return $this->model->where('hash', $hash)->firstOrFail();
    }
}
