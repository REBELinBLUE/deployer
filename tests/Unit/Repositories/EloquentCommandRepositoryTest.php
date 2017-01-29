<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Repositories\Contracts\CommandRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentCommandRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentCommandRepository
 */
class EloquentCommandRepositoryTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Command::class);
        $repository = new EloquentCommandRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsServerRepositoryInterface()
    {
        $model      = m::mock(Command::class);
        $repository = new EloquentCommandRepository($model);

        $this->assertInstanceOf(CommandRepositoryInterface::class, $repository);
    }

    /**
     * @covers ::updateById
     */
    public function testUpdateById()
    {
        $id     = 1;
        $fields = ['foo' => 'bar'];

        $expected = m::mock(Command::class);
        $expected->shouldReceive('update')->once()->with($fields);
        $expected->shouldReceive('getAttribute')->with('servers');

        $model = m::mock(Command::class);
        $model->shouldReceive('findOrFail')->once()->with($id)->andReturn($expected);

        $repository = new EloquentCommandRepository($model);
        $actual     = $repository->updateById($fields, $id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::updateById
     */
    public function testUpdateByIdSyncsServers()
    {
        $id             = 1;
        $expectedServer = 'a-server';
        $fields         = ['foo' => 'bar', 'servers' => $expectedServer];
        $update         = ['foo' => 'bar'];

        $server = m::mock(Server::class);
        $server->shouldReceive('sync')->once()->with($expectedServer);

        $expected = m::mock(Command::class);
        $expected->shouldReceive('update')->once()->with($update);
        $expected->shouldReceive('servers')->once()->andReturn($server);
        $expected->shouldReceive('getAttribute')->with('servers');

        $model = m::mock(Command::class);
        $model->shouldReceive('findOrFail')->once()->with($id)->andReturn($expected);

        $repository = new EloquentCommandRepository($model);
        $actual     = $repository->updateById($fields, $id);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $this->markTestSkipped('not working - same issue as ProjectRepository');
    }

    /**
     * @covers ::getForDeployStep
     */
    public function testGetForDeployStep()
    {
        $this->markTestSkipped('not working - same issue as ProjectRepository');

        $target_id = 1;
        $target    = 'template';
        $step      = 2;

        $expected = m::mock(Command::class);

        $model = m::mock(Command::class);
        $model->shouldReceive('with')->once()->with('servers')->andReturn();
        $model->shouldReceive('where')->once()->with('target_type', $target)->andReturnSelf();
        $model->shouldReceive('where')->once()->with('target_id', $target_id)->andReturnSelf();
        $model->shouldReceive('whereIn')->once()->with('step', [1, 3])->andReturnSelf();
        $model->shouldReceive('orderBy')->once()->with('order')->andReturnSelf();
        $model->shouldReceive('get')->once()->andReturn($expected);

        $repository = new EloquentCommandRepository($model);
        $actual     = $repository->getForDeployStep($target_id, $target, $step);

        $this->assertSame($expected, $actual);
    }
}
