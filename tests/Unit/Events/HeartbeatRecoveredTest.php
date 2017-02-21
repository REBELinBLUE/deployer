<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events;

use Mockery as m;
use REBELinBLUE\Deployer\Events\HeartbeatRecovered;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\HeartbeatRecovered
 */
class HeartbeatRecoveredTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers \REBELinBLUE\Deployer\Events\HeartbeatChanged::__construct
     */
    public function testHeartbeatProperty()
    {
        $heartbeat = m::mock(Heartbeat::class);

        $event = new HeartbeatRecovered($heartbeat);

        $this->assertSame($heartbeat, $event->heartbeat);
    }
}
