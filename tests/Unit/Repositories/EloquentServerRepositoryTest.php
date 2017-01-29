<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Jobs\TestServerConnection;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Repositories\EloquentServerRepository;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentServerRepository
 */
class EloquentServerRepositoryTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Server::class);
        $repository = new EloquentServerRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsServerRepositoryInterface()
    {
        $model      = m::mock(Server::class);
        $repository = new EloquentServerRepository($model);

        $this->assertInstanceOf(ServerRepositoryInterface::class, $repository);
    }

    /**
     * @covers ::getAll
     */
    public function testGetAll()
    {
        $expected = m::mock(Server::class);
        $expected->shouldReceive('get')->andReturnSelf();

        $model  = m::mock(Server::class);
        $model->shouldReceive('orderBy')->once()->with('name')->andReturn($expected);

        $repository = new EloquentServerRepository($model);
        $actual     = $repository->getAll();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::queueForTesting
     */
    public function testQueueForTesting()
    {
        $id = 1;

        $expected = m::mock(Server::class);
        $expected->shouldReceive('isTesting')->once()->andReturn(false);
        $expected->shouldReceive('setAttribute')->once()->with('status', Server::TESTING);
        $expected->shouldReceive('save')->once()->andReturnSelf();

        $this->expectsJobs(TestServerConnection::class);

        $model = m::mock(Server::class);
        $model->shouldReceive('findOrFail')->once()->with($id)->andReturn($expected);

        $repository = new EloquentServerRepository($model);
        $repository->queueForTesting($id);
    }

    /**
     * @covers ::queueForTesting
     */
    public function testQueueForTestingDoesNotQueueWhenQueued()
    {
        $id = 1;

        $expected = m::mock(Server::class);
        $expected->shouldReceive('isTesting')->once()->andReturn(true);

        $this->doesntExpectJobs(TestServerConnection::class);

        $model = m::mock(Server::class);
        $model->shouldReceive('findOrFail')->once()->with($id)->andReturn($expected);

        $repository = new EloquentServerRepository($model);
        $repository->queueForTesting($id);
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $this->markTestSkipped('not working - same issue as ProjectRepository');

        $project_id = 1;
        $fields     = ['foo' => 'bar', 'project_id' => $project_id];
        $create     = ['foo' => 'bar', 'project_id' => $project_id, 'order' => 0];

        $expected = m::mock(Server::class);

        $model = m::mock(Server::class);
        $model->shouldReceive('where')->once()->with('project_id', $project_id)->andReturnSelf();
        $model->shouldReceive('orderBy')->once()->with('order', 'DESC')->andReturnSelf();
        $model->shouldReceive('first')->once()->andReturnNull();

        $model->shouldReceive('create')->once()->with($create)->andReturn($expected);

        $repository = new EloquentServerRepository($model);
        $actual     = $repository->create($fields);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::queryByName
     */
    public function testQueryByName()
    {
        $expected = m::mock(Server::class);
        $expected->shouldReceive('get')->andReturnSelf();

        $model  = m::mock(Server::class);
        $model->shouldReceive('where')->once()->with('name', 'LIKE', '%server%')->andReturn($expected);

        $repository = new EloquentServerRepository($model);
        $actual     = $repository->queryByName('server');

        $this->assertSame($expected, $actual);
    }
}
