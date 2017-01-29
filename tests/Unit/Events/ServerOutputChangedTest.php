<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Mockery as m;
use REBELinBLUE\Deployer\Events\ServerOutputChanged;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\ServerOutputChanged
 */
class ServerOutputChangedTest extends TestCase
{
    /**
     * @covers ::broadcastOn
     */
    public function testBroadcastOn()
    {
        $id = time();

        $event = new ServerOutputChanged($this->mockServerlog($id));
        $this->assertSame(['serverlog-' . $id], $event->broadcastOn());
    }

    /**
     * @covers ::__construct
     */
    public function testIsBroadcastable()
    {
        $event = new ServerOutputChanged($this->mockServerlog(10));
        $this->assertInstanceOf(ShouldBroadcast::class, $event);
    }

    private function mockServerlog($id)
    {
        $log = m::mock(ServerLog::class);
        $log->shouldReceive('getAttribute')->once()->with('id')->andReturn($id);
        $log->shouldReceive('getAttribute')->once()->with('output')->andReturn(null);

        return $log;
    }
}
