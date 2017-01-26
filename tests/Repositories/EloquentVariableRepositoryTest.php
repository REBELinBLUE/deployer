<?php

namespace REBELinBLUE\Deployer\Tests\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Repositories\Contracts\VariableRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Repositories\EloquentVariableRepository;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Variable;

class EloquentVariableRepositoryTest extends TestCase
{
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Variable::class);
        $repository = new EloquentVariableRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function testImplementsVariableRepositoryInterfaceInterface()
    {
        $model      = m::mock(Variable::class);
        $repository = new EloquentVariableRepository($model);

        $this->assertInstanceOf(VariableRepositoryInterface::class, $repository);
    }
}
