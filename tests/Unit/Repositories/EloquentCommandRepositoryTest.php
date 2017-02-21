<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Repositories\Contracts\CommandRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentCommandRepository;
use REBELinBLUE\Deployer\Server;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentCommandRepository
 */
class EloquentCommandRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(Command::class, EloquentCommandRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsServerRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            Command::class,
            EloquentCommandRepository::class,
            CommandRepositoryInterface::class
        );
    }

    /**
     * @covers ::updateById
     */
    public function testUpdateById()
    {
        $model_id     = 1;
        $fields       = ['foo' => 'bar'];

        $expected = m::mock(Command::class);
        $expected->shouldReceive('update')->once()->with($fields);
        $expected->shouldReceive('getAttribute')->with('servers');

        $model = m::mock(Command::class);
        $model->shouldReceive('findOrFail')->once()->with($model_id)->andReturn($expected);

        $repository = new EloquentCommandRepository($model);
        $actual     = $repository->updateById($fields, $model_id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::updateById
     */
    public function testUpdateByIdSyncsServers()
    {
        $expectedId             = 1;
        $expectedServer         = 'a-server';
        $fields                 = ['foo' => 'bar', 'servers' => $expectedServer];
        $update                 = ['foo' => 'bar'];

        $server = m::mock(Server::class);
        $server->shouldReceive('sync')->once()->with($expectedServer);

        $expected = m::mock(Command::class);
        $expected->shouldReceive('update')->once()->with($update);
        $expected->shouldReceive('servers')->once()->andReturn($server);
        $expected->shouldReceive('getAttribute')->with('servers');

        $model = m::mock(Command::class);
        $model->shouldReceive('findOrFail')->once()->with($expectedId)->andReturn($expected);

        $repository = new EloquentCommandRepository($model);
        $actual     = $repository->updateById($fields, $expectedId);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $target_id  = 1;
        $target     = 'project';
        $step       = Command::BEFORE_CLONE;
        $fields     = ['foo' => 'bar', 'target_type' => $target, 'target_id' => $target_id, 'step' => $step];
        $create     = array_merge($fields, ['order' => 0]);

        $expected = m::mock(Command::class);
        $expected->shouldReceive('getAttribute')->with('servers');

        $model = m::mock(Command::class);
        $model->shouldReceive('where')->once()->with('target_type', $target)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('target_id', $target_id)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('step', $step)->andReturnSelf();
        $model->shouldReceive('orderBy')->once()->with('order', 'DESC')->andReturnSelf();
        $model->shouldReceive('first')->once()->andReturnNull();

        $model->shouldReceive('create')->once()->with($create)->andReturn($expected);

        $repository = new EloquentCommandRepository($model);
        $actual     = $repository->create($fields);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::create
     */
    public function testCreateWithExistingCommandUsesNextOrder()
    {
        $target_id  = 1;
        $target     = 'project';
        $step       = Command::BEFORE_CLONE;
        $fields     = ['foo' => 'bar', 'target_type' => $target, 'target_id' => $target_id, 'step' => $step];
        $create     = array_merge($fields, ['order' => 6]);

        $expected = m::mock(Command::class);
        $expected->shouldReceive('getAttribute')->with('servers');

        $model = m::mock(Command::class);
        $model->shouldReceive('where')->once()->with('target_type', $target)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('target_id', $target_id)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('step', $step)->andReturnSelf();
        $model->shouldReceive('orderBy')->once()->with('order', 'DESC')->andReturnSelf();
        $model->shouldReceive('first')->once()->andReturn((object) ['order' => 5]);

        $model->shouldReceive('create')->once()->with($create)->andReturn($expected);

        $repository = new EloquentCommandRepository($model);
        $actual     = $repository->create($fields);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::create
     */
    public function testCreateWithServers()
    {
        $target_id  = 1;
        $target     = 'project';
        $step       = Command::BEFORE_CLONE;
        $fields     = [
            'servers'     => ['a-server-id'],
            'foo'         => 'bar',
            'target_type' => $target,
            'target_id'   => $target_id,
            'step'        => $step,
        ];

        $create     = [
            'foo'         => 'bar',
            'target_type' => $target,
            'target_id'   => $target_id,
            'step'        => $step,
            'order'       => 0,
        ];

        $servers = m::mock(Server::class);
        $servers->shouldReceive('sync')->once()->with(['a-server-id']);

        $expected = m::mock(Command::class);
        $expected->shouldReceive('getAttribute')->with('servers');
        $expected->shouldReceive('servers')->andReturn($servers);

        $model = m::mock(Command::class);
        $model->shouldReceive('where')->once()->with('target_type', $target)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('target_id', $target_id)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('step', $step)->andReturnSelf();
        $model->shouldReceive('orderBy')->once()->with('order', 'DESC')->andReturnSelf();
        $model->shouldReceive('first')->once()->andReturnNull();

        $model->shouldReceive('create')->once()->with($create)->andReturn($expected);

        $repository = new EloquentCommandRepository($model);
        $actual     = $repository->create($fields);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getForDeployStep
     */
    public function testGetForDeployStep()
    {
        $target_id = 1;
        $target    = 'template';
        $step      = 2;

        $expected = m::mock(Command::class);
        $expected->shouldReceive('with')->once()->with('servers')->andReturnSelf();
        $expected->shouldReceive('whereIn')->once()->with('step', [1, 3])->andReturnSelf();
        $expected->shouldReceive('orderBy')->once()->with('order')->andReturnSelf();
        $expected->shouldReceive('get')->once()->andReturnSelf();

        $model = m::mock(Command::class);
        $model->shouldReceive('where')->once()->with('target_type', $target)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('target_id', $target_id)->andReturn($expected);

        $repository = new EloquentCommandRepository($model);
        $actual     = $repository->getForDeployStep($target_id, $target, $step);

        $this->assertSame($expected, $actual);
    }
}
