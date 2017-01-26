<?php

namespace REBELinBLUE\Deployer\Tests\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Group;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentGroupRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

class EloquentGroupRepositoryTest extends TestCase
{
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Group::class);
        $repository = new EloquentGroupRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function testImplementsGroupRepositoryInterfaceInterface()
    {
        $model      = m::mock(Group::class);
        $repository = new EloquentGroupRepository($model);

        $this->assertInstanceOf(GroupRepositoryInterface::class, $repository);
    }

    public function testGetAll()
    {
        $expected = m::mock(Group::class);
        $expected->shouldReceive('get')->andReturnSelf();

        $model  = m::mock(Group::class);
        $model->shouldReceive('orderBy')->with('order')->andReturn($expected);

        $repository = new EloquentGroupRepository($model);
        $actual     = $repository->getAll();

        $this->assertEquals($expected, $actual);
    }
}
