<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Contracts\Repositories\HeartbeatRepositoryInterface;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;

/**
 * The shared file repository.
 */
class EloquentHeartbeatRepository extends EloquentRepository implements HeartbeatRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param  Heartbeat                    $model
     * @return HeartbeatRepositoryInterface
     */
    public function __construct(Heartbeat $model)
    {
        $this->model = $model;
    }

    /**
     * Gets a heartbeat by it's hash.
     *
     * @param  string    $hash
     * @return Heartbeat
     */
    public function getByHash($hash)
    {
        return $this->model->where('hash', $hash)->firstOrFail();
    }
}
