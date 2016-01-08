<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Variable;
use REBELinBLUE\Deployer\Repositories\Contracts\VariableRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;

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
