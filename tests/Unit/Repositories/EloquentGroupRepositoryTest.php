<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentGroupRepository;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentGroupRepository
 */
class EloquentGroupRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(Group::class, EloquentGroupRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsGroupRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            Group::class,
            EloquentGroupRepository::class,
            GroupRepositoryInterface::class
        );
    }

    /**
     * @covers ::getAll
     */
    public function testGetAll()
    {
        $expected = m::mock(Group::class);
        $expected->shouldReceive('get')->andReturnSelf();

        $model  = m::mock(Group::class);
        $model->shouldReceive('orderBy')->once()->with('order')->andReturn($expected);

        $repository = new EloquentGroupRepository($model);
        $actual     = $repository->getAll();

        $this->assertSame($expected, $actual);
    }
}
