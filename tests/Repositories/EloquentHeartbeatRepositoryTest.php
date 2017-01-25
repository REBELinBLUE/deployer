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
        $heartbeat  = m::mock(Heartbeat::class);
        $repository = new EloquentHeartbeatRepository($heartbeat);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function testImplementsHeartbeatRepositoryInterface()
    {
        $heartbeat  = m::mock(Heartbeat::class);
        $repository = new EloquentHeartbeatRepository($heartbeat);

        $this->assertInstanceOf(HeartbeatRepositoryInterface::class, $repository);
    }

    public function testGetByHash()
    {
        $hash = 'a-heartbeat-hash';

        $expected = m::mock(Heartbeat::class);
        $expected->shouldReceive('firstOrFail')->andReturnSelf();

        $heartbeat = m::mock(Heartbeat::class);
        $heartbeat->shouldReceive('where')->with('hash', $hash)->andReturn($expected);

        $repository = new EloquentHeartbeatRepository($heartbeat);
        $actual     = $repository->getByHash($hash);

        $this->assertEquals($expected, $actual);
    }

    public function testGetByHashShouldThrowModelNotFoundException()
    {
        $hash = 'a-heartbeat-hash';

        $this->expectException(ModelNotFoundException::class);

        $expected = m::mock(Heartbeat::class);
        $expected->shouldReceive('firstOrFail')->andThrow(ModelNotFoundException::class);

        $heartbeat = m::mock(Heartbeat::class);
        $heartbeat->shouldReceive('where')->with('hash', $hash)->andReturn($expected);

        $repository = new EloquentHeartbeatRepository($heartbeat);
        $repository->getByHash($hash);
    }
}
