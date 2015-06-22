<?php

namespace App\Repositories;

use App\Heartbeat;
use App\Repositories\Contracts\HeartbeatRepositoryInterface;
use App\Repositories\EloquentRepository;

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
