<?php

namespace REBELinBLUE\Deployer\Tests\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery as m;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Repositories\Contracts\HeartbeatRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentHeartbeatRepository;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Tests\TestCase;

class EloquentHeartbeatRepositoryTest extends TestCase
{
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Heartbeat::class);
        $repository = new EloquentHeartbeatRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function testImplementsHeartbeatRepositoryInterface()
    {
        $model      = m::mock(Heartbeat::class);
        $repository = new EloquentHeartbeatRepository($model);

        $this->assertInstanceOf(HeartbeatRepositoryInterface::class, $repository);
    }

    public function testGetByHash()
    {
        $hash = 'a-heartbeat-hash';

        $expected = m::mock(Heartbeat::class);
        $expected->shouldReceive('firstOrFail')->andReturnSelf();

        $model = m::mock(Heartbeat::class);
        $model->shouldReceive('where')->with('hash', $hash)->andReturn($expected);

        $repository = new EloquentHeartbeatRepository($model);
        $actual     = $repository->getByHash($hash);

        $this->assertEquals($expected, $actual);
    }

    public function testGetByHashShouldThrowModelNotFoundException()
    {
        $hash = 'a-heartbeat-hash';

        $this->expectException(ModelNotFoundException::class);

        $expected = m::mock(Heartbeat::class);
        $expected->shouldReceive('firstOrFail')->andThrow(ModelNotFoundException::class);

        $model = m::mock(Heartbeat::class);
        $model->shouldReceive('where')->with('hash', $hash)->andReturn($expected);

        $repository = new EloquentHeartbeatRepository($model);
        $repository->getByHash($hash);
    }
}
