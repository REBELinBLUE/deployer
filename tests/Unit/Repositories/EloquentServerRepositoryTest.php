<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mockery as m;
use REBELinBLUE\Deployer\Jobs\TestServerConnection;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentServerRepository;
use REBELinBLUE\Deployer\Server;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentServerRepository
 */
class EloquentServerRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(Server::class, EloquentServerRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsServerRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            Server::class,
            EloquentServerRepository::class,
            ServerRepositoryInterface::class
        );
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
        $server_id = 1;

        $expected = m::mock(Server::class);
        $expected->shouldReceive('isTesting')->once()->andReturn(false);
        $expected->shouldReceive('setAttribute')->once()->with('status', Server::TESTING);
        $expected->shouldReceive('save')->once()->andReturnSelf();

        $this->expectsJobs(TestServerConnection::class);

        $model = m::mock(Server::class);
        $model->shouldReceive('findOrFail')->once()->with($server_id)->andReturn($expected);

        $repository = new EloquentServerRepository($model);
        $repository->queueForTesting($server_id);
    }

    /**
     * @covers ::queueForTesting
     */
    public function testQueueForTestingDoesNotQueueWhenQueued()
    {
        $server_id = 1;

        $expected = m::mock(Server::class);
        $expected->shouldReceive('isTesting')->once()->andReturn(true);

        $this->doesntExpectJobs(TestServerConnection::class);

        $model = m::mock(Server::class);
        $model->shouldReceive('findOrFail')->once()->with($server_id)->andReturn($expected);

        $repository = new EloquentServerRepository($model);
        $repository->queueForTesting($server_id);
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
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
     * @covers ::create
     */
    public function testCreateWithExistingProjectsUsesNextOrder()
    {
        $project_id = 1;
        $fields     = ['foo' => 'bar', 'project_id' => $project_id];
        $create     = ['foo' => 'bar', 'project_id' => $project_id, 'order' => 10];

        $expected = m::mock(Server::class);

        $model = m::mock(Server::class);
        $model->shouldReceive('where')->once()->with('project_id', $project_id)->andReturnSelf();
        $model->shouldReceive('orderBy')->once()->with('order', 'DESC')->andReturnSelf();
        $model->shouldReceive('first')->once()->andReturn((object) ['order' => 9]);

        $model->shouldReceive('create')->once()->with($create)->andReturn($expected);

        $repository = new EloquentServerRepository($model);
        $actual     = $repository->create($fields);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideCommands
     * @covers ::create
     */
    public function testCreateWithCommands($hasCommands)
    {
        $project_id = 1;
        $server_id  = 12345;
        $fields     = ['foo' => 'bar', 'project_id' => $project_id, 'add_commands' => $hasCommands];
        $create     = ['foo' => 'bar', 'project_id' => $project_id, 'order' => 0];

        $expected = m::mock(Server::class);

        $server = m::mock(Server::class);
        $server->shouldReceive('where')->once()->with('project_id', $project_id)->andReturnSelf();
        $server->shouldReceive('orderBy')->once()->with('order', 'DESC')->andReturnSelf();
        $server->shouldReceive('first')->once()->andReturnNull();
        $server->shouldReceive('create')->once()->with($create)->andReturn($expected);

        if ($hasCommands) {
            $expected->shouldReceive('getAttribute')->once()->with('id')->andReturn($server_id);

            $servers = m::mock(BelongsToMany::class);
            $servers->shouldReceive('attach')->with($server_id);

            $command = m::mock(Command::class);
            $command->shouldReceive('servers')->andReturn($servers);

            $project = m::mock(Project::class);
            $project->shouldReceive('getAttribute')->once()->with('commands')->andReturn([$command]);

            $expected->shouldReceive('getAttribute')->once()->with('project')->andReturn($project);
        }

        $repository = new EloquentServerRepository($server);
        $actual     = $repository->create($fields);

        $this->assertSame($expected, $actual);
    }

    public function provideCommands()
    {
        return $this->fixture('Repositories/EloquentServerRepository');
    }
}
