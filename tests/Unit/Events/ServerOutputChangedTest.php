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
        $expected_id = 12345;

        $event = new ServerOutputChanged($this->mockServerlog($expected_id));
        $this->assertSame(['serverlog-' . $expected_id], $event->broadcastOn());
    }

    /**
     * @covers ::__construct
     */
    public function testIsBroadcastable()
    {
        $event = new ServerOutputChanged($this->mockServerlog(10));
        $this->assertInstanceOf(ShouldBroadcast::class, $event);
    }

    private function mockServerlog($expected_id)
    {
        $log = m::mock(ServerLog::class);
        $log->shouldReceive('getAttribute')->once()->with('id')->andReturn($expected_id);
        $log->shouldReceive('getAttribute')->once()->with('output')->andReturn(null);

        return $log;
    }
}
