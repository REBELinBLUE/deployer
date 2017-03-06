<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
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

        $log     = new ServerLog();
        $log->id = $expected_id;

        $event = new ServerOutputChanged($log);
        $this->assertSame(['serverlog-' . $expected_id], $event->broadcastOn());
    }

    /**
     * @covers ::__construct
     */
    public function testIsBroadcastable()
    {
        $log     = new ServerLog();
        $log->id = 10;

        $event = new ServerOutputChanged($log);
        $this->assertInstanceOf(ShouldBroadcast::class, $event);
    }
}
