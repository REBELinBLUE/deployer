<?php

namespace REBELinBLUE\Deployer\Tests\Repositories;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\Repositories\Stubs\StubEloquentRepository;
use REBELinBLUE\Deployer\Tests\Repositories\Stubs\StubModel;
use REBELinBLUE\Deployer\Tests\TestCase;

class EloquentRepositoryTest extends TestCase
{
    public function testGetAll()
    {
        $expected = 'all-models';

        $model = m::mock(StubModel::class);
        $model->shouldReceive('all')->andReturn($expected);

        $repository = new StubEloquentRepository($model);
        $actual     = $repository->getAll();

        $this->assertEquals($expected, $actual);
    }

    public function testGetById()
    {
        $expected = 'a-model';
        $id       = 1;

        $model = m::mock(StubModel::class);
        $model->shouldReceive('findOrFail')->with($id)->andReturn($expected);

        $repository = new StubEloquentRepository($model);
        $actual     = $repository->getById($id);

        $this->assertEquals($expected, $actual);
    }

    public function testGetByIdThrowsModelNotFoundException()
    {
        $id = 1;
        $this->expectException(ModelNotFoundException::class);

        $model = m::mock(StubModel::class);
        $model->shouldReceive('findOrFail')->with($id)->andThrow(ModelNotFoundException::class);

        $repository = new StubEloquentRepository($model);
        $repository->getById($id);
    }

    public function testCreate()
    {
        $expected = 'a-model';
        $fields   = ['foo' => 'bar'];

        $model = m::mock(StubModel::class);
        $model->shouldReceive('create')->with($fields)->andReturn($expected);

        $repository = new StubEloquentRepository($model);
        $actual     = $repository->create($fields);

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateById()
    {
        $id     = 1;
        $fields = ['foo' => 'bar'];

        $expected = m::mock(StubModel::class);
        $expected->shouldReceive('update')->with($fields);

        $model = m::mock(StubModel::class);
        $model->shouldReceive('findOrFail')->with($id)->andReturn($expected);

        $repository = new StubEloquentRepository($model);
        $actual     = $repository->updateById($fields, $id);

        $this->assertEquals($expected, $actual);
    }

    public function testDeleteById()
    {
        $id = 1;

        $found = m::mock(StubModel::class);
        $found->shouldReceive('delete')->andReturn(true);

        $model = m::mock(StubModel::class);
        $model->shouldReceive('findOrFail')->with($id)->andReturn($found);

        $repository = new StubEloquentRepository($model);
        $actual     = $repository->deleteById($id);

        $this->assertTrue($actual);
    }

    public function testDeleteByIdThrowsException()
    {
        $this->expectException(Exception::class);

        $id = 1;

        $found = m::mock(StubModel::class);
        $found->shouldReceive('delete')->andThrow(Exception::class);

        $model = m::mock(StubModel::class);
        $model->shouldReceive('findOrFail')->with($id)->andReturn($found);

        $repository = new StubEloquentRepository($model);
        $repository->deleteById($id);
    }
}
