<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use REBELinBLUE\Deployer\Events\RestartSocketServer;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\RestartSocketServer
 */
class RestartSocketServerTest extends TestCase
{
    /**
     * @covers ::broadcastOn
     */
    public function testBroadcastOn()
    {
        $event = new RestartSocketServer();
        $this->assertSame(['restart'], $event->broadcastOn());
    }

    /**
     * @covers ::__construct
     */
    public function testIsBroadcastable()
    {
        $event = new RestartSocketServer();
        $this->assertInstanceOf(ShouldBroadcast::class, $event);
    }
}
