<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Contracts\Repositories\VariableRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Variable;

/**
 * The variable repository.
 */
class EloquentVariableRepository extends EloquentRepository implements VariableRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param  Variable                   $model
     * @return EloquentVariableRepository
     */
    public function __construct(Variable $model)
    {
        $this->model = $model;
    }
}
