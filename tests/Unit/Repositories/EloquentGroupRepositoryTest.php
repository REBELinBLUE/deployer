<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentGroupRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentGroupRepository
 */
class EloquentGroupRepositoryTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Group::class);
        $repository = new EloquentGroupRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsGroupRepositoryInterface()
    {
        $model      = m::mock(Group::class);
        $repository = new EloquentGroupRepository($model);

        $this->assertInstanceOf(GroupRepositoryInterface::class, $repository);
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
