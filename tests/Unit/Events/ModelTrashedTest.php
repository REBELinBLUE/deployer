<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Mockery as m;
use REBELinBLUE\Deployer\Events\ModelTrashed;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\ModelTrashed
 */
class ModelTrashedTest extends TestCase
{
    /**
     * @covers ::broadcastOn
     */
    public function testBroadcastOn()
    {
        $expected = ['id' => 1, 'project_id' => 2];
        $model    = $this->mockModel($expected['id'], $expected['project_id']);

        $channel = 'some-broadcast-channel';

        $event = new ModelTrashed($model, $channel);
        $this->assertSame([$channel], $event->broadcastOn());
        $this->assertSame($expected, $event->model);
    }

    /**
     * @covers ::__construct
     */
    public function testIsBroadcastable()
    {
        $model = $this->mockModel(1, 2);

        $event = new ModelTrashed($model, 'a-channel');
        $this->assertInstanceOf(ShouldBroadcast::class, $event);
    }

    private function mockModel($server_id, $project_id)
    {
        $model = m::mock(Server::class);
        $model->shouldReceive('getAttribute')->once()->with('id')->andReturn($server_id);
        $model->shouldReceive('getAttribute')->once()->with('project_id')->andReturn($project_id);

        return $model;
    }
}
