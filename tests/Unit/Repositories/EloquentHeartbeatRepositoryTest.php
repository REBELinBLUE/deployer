<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery as m;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Repositories\Contracts\HeartbeatRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentHeartbeatRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentHeartbeatRepository
 */
class EloquentHeartbeatRepositoryTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Heartbeat::class);
        $repository = new EloquentHeartbeatRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsHeartbeatRepositoryInterface()
    {
        $model      = m::mock(Heartbeat::class);
        $repository = new EloquentHeartbeatRepository($model);

        $this->assertInstanceOf(HeartbeatRepositoryInterface::class, $repository);
    }

    /**
     * @covers ::getByHash
     */
    public function testGetByHash()
    {
        $hash = 'a-heartbeat-hash';

        $expected = m::mock(Heartbeat::class);
        $expected->shouldReceive('firstOrFail')->andReturnSelf();

        $model = m::mock(Heartbeat::class);
        $model->shouldReceive('where')->once()->with('hash', $hash)->andReturn($expected);

        $repository = new EloquentHeartbeatRepository($model);
        $actual     = $repository->getByHash($hash);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getByHash
     */
    public function testGetByHashShouldThrowModelNotFoundException()
    {
        $hash = 'a-heartbeat-hash';

        $this->expectException(ModelNotFoundException::class);

        $expected = m::mock(Heartbeat::class);
        $expected->shouldReceive('firstOrFail')->andThrow(ModelNotFoundException::class);

        $model = m::mock(Heartbeat::class);
        $model->shouldReceive('where')->once()->with('hash', $hash)->andReturn($expected);

        $repository = new EloquentHeartbeatRepository($model);
        $repository->getByHash($hash);
    }
}
