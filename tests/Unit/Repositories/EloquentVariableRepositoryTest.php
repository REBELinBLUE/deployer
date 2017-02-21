<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\VariableRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentVariableRepository;
use REBELinBLUE\Deployer\Variable;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentVariableRepository
 */
class EloquentVariableRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(Variable::class, EloquentVariableRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsVariableRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            Variable::class,
            EloquentVariableRepository::class,
            VariableRepositoryInterface::class
        );
    }
}
