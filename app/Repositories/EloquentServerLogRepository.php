<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\ServerLogRepositoryInterface;
use REBELinBLUE\Deployer\ServerLog;

/**
 * Server log repository.
 */
class EloquentServerLogRepository extends EloquentRepository implements ServerLogRepositoryInterface
{
    /**
     * EloquentServerLogRepository constructor.
     *
     * @param ServerLog $model
     */
    public function __construct(ServerLog $model)
    {
        $this->model = $model;
    }
}
