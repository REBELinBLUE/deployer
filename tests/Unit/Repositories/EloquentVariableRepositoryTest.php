<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Repositories\Contracts\VariableRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Repositories\EloquentVariableRepository;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Variable;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentVariableRepository
 */
class EloquentVariableRepositoryTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Variable::class);
        $repository = new EloquentVariableRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsVariableRepositoryInterface()
    {
        $model      = m::mock(Variable::class);
        $repository = new EloquentVariableRepository($model);

        $this->assertInstanceOf(VariableRepositoryInterface::class, $repository);
    }
}
