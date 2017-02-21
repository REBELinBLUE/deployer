<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Mockery as m;
use REBELinBLUE\Deployer\Events\ModelCreated;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\ModelCreated
 */
class ModelCreatedTest extends TestCase
{
    /**
     * @covers ::broadcastOn
     */
    public function testBroadcastOn()
    {
        $model    = m::mock(Server::class);
        $expected = 'some-broadcast-channel';

        $event = new ModelCreated($model, $expected);
        $this->assertSame([$expected], $event->broadcastOn());
        $this->assertSame($model, $event->model);
    }

    /**
     * @covers ::__construct
     */
    public function testIsBroadcastable()
    {
        $model = m::mock(Server::class);

        $event = new ModelCreated($model, 'a-channel');
        $this->assertInstanceOf(ShouldBroadcast::class, $event);
    }
}
