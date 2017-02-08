<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Ref;
use REBELinBLUE\Deployer\Repositories\Contracts\RefRepositoryInterface;

class EloquentRefRepository extends EloquentRepository implements RefRepositoryInterface
{
    /**
     * EloquentRefRepository constructor.
     *
     * @param Ref $model
     */
    public function __construct(Ref $model)
    {
        $this->model = $model;
    }
}
