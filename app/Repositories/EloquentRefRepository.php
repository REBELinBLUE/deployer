<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Ref;
use REBELinBLUE\Deployer\Repositories\Contracts\RefRepositoryInterface;

/**
 * The repository for git branches/tags.
 */
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
