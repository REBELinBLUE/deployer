<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Repositories\Contracts\DeployStepRepositoryInterface;

/**
 * Deploy step repository.
 */
class EloquentDeployStepRepository extends EloquentRepository implements DeployStepRepositoryInterface
{
    /**
     * EloquentDeployStepRepository constructor.
     *
     * @param DeployStep $model
     */
    public function __construct(DeployStep $model)
    {
        $this->model = $model;
    }
}
