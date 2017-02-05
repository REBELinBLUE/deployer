<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\stubs\EloquentRepository as StubEloquentRepository;
use REBELinBLUE\Deployer\Tests\Unit\stubs\Model as StubModel;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentRepository
 */
class EloquentRepositoryTest extends TestCase
{
    /**
     * @covers ::getAll
     */
    public function testGetAll()
    {
        $expected = 'all-models';

        $model = m::mock(StubModel::class);
        $model->shouldReceive('all')->andReturn($expected);

        $repository = new StubEloquentRepository($model);
        $actual     = $repository->getAll();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getById
     */
    public function testGetById()
    {
        $expected = 'a-model';
        $id       = 1;

        $model = m::mock(StubModel::class);
        $model->shouldReceive('findOrFail')->once()->with($id)->andReturn($expected);

        $repository = new StubEloquentRepository($model);
        $actual     = $repository->getById($id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getById
     */
    public function testGetByIdThrowsModelNotFoundException()
    {
        $id = 1;
        $this->expectException(ModelNotFoundException::class);

        $model = m::mock(StubModel::class);
        $model->shouldReceive('findOrFail')->once()->with($id)->andThrow(ModelNotFoundException::class);

        $repository = new StubEloquentRepository($model);
        $repository->getById($id);
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $expected = 'a-model';
        $fields   = ['foo' => 'bar'];

        $model = m::mock(StubModel::class);
        $model->shouldReceive('create')->once()->with($fields)->andReturn($expected);

        $repository = new StubEloquentRepository($model);
        $actual     = $repository->create($fields);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::updateById
     */
    public function testUpdateById()
    {
        $id     = 1;
        $fields = ['foo' => 'bar'];

        $expected = m::mock(StubModel::class);
        $expected->shouldReceive('update')->once()->with($fields);

        $model = m::mock(StubModel::class);
        $model->shouldReceive('findOrFail')->once()->with($id)->andReturn($expected);

        $repository = new StubEloquentRepository($model);
        $actual     = $repository->updateById($fields, $id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::deleteById
     */
    public function testDeleteById()
    {
        $id = 1;

        $found = m::mock(StubModel::class);
        $found->shouldReceive('delete')->andReturn(true);

        $model = m::mock(StubModel::class);
        $model->shouldReceive('findOrFail')->once()->with($id)->andReturn($found);

        $repository = new StubEloquentRepository($model);
        $actual     = $repository->deleteById($id);

        $this->assertTrue($actual);
    }

    /**
     * @covers ::deleteById
     */
    public function testDeleteByIdThrowsException()
    {
        $this->expectException(Exception::class);

        $id = 1;

        $found = m::mock(StubModel::class);
        $found->shouldReceive('delete')->andThrow(Exception::class);

        $model = m::mock(StubModel::class);
        $model->shouldReceive('findOrFail')->once()->with($id)->andReturn($found);

        $repository = new StubEloquentRepository($model);
        $repository->deleteById($id);
    }
}
