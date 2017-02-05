<?php

namespace REBELinBLUE\Deployer\Tests\Unit\stubs;

use REBELinBLUE\Deployer\Repositories\EloquentRepository as BaseEloquentRepository;

class EloquentRepository extends BaseEloquentRepository
{
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
