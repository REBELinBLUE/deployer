<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Repositories\Contracts\CheckUrlRepositoryInterface;

/**
 * The check url repository.
 */
class EloquentCheckUrlRepository extends EloquentRepository implements CheckUrlRepositoryInterface
{
    /**
     * EloquentCheckUrlRepository constructor.
     *
     * @param CheckUrl $model
     */
    public function __construct(CheckUrl $model)
    {
        $this->model = $model;
    }
}
