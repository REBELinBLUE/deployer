<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Database;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Events\HeartbeatRecovered;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\BroadcastChanges;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Heartbeat
 * @group slow
 */
class HeartbeatTest extends TestCase
{
    use DatabaseMigrations, BroadcastChanges;

    /**
     * @covers ::pinged
     */
    public function testPinged()
    {
        /** @var Heartbeat $heartbeat */
        $heartbeat = factory(Heartbeat::class)->create([
            'status' => Heartbeat::OK,
        ]);

        $this->doesntExpectEvents(HeartbeatRecovered::class);

        Carbon::setTestNow(Carbon::create(2016, 1, 1, 12, 15, 00, 'UTC'));

        $heartbeat->pinged();

        $this->assertSame(Heartbeat::OK, $heartbeat->status);
        $this->assertSame(0, $heartbeat->missed);
        $this->assertSameTimestamp('2016-01-01 12:15:00', $heartbeat->last_activity);
    }

    /**
     * @covers ::pinged
     */
    public function testPingedDoesNotDispatchEventWhenPreviouslyUntested()
    {
        /** @var Heartbeat $heartbeat */
        $heartbeat = factory(Heartbeat::class)->create([
            'status' => Heartbeat::UNTESTED,
        ]);

        $this->doesntExpectEvents(HeartbeatRecovered::class);

        Carbon::setTestNow(Carbon::create(2016, 1, 1, 12, 15, 00, 'UTC'));

        $heartbeat->pinged();

        $this->assertSame(Heartbeat::OK, $heartbeat->status);
        $this->assertSame(0, $heartbeat->missed);
        $this->assertSameTimestamp('2016-01-01 12:15:00', $heartbeat->last_activity);
    }

    /**
     * @covers ::pinged
     */
    public function testPingedDispatchesEventPreviouslyOffline()
    {
        /** @var Heartbeat $heartbeat */
        $heartbeat = factory(Heartbeat::class)->create([
            'status' => Heartbeat::MISSING,
            'missed' => 3,
        ]);

        $this->expectsEvents(HeartbeatRecovered::class);

        Carbon::setTestNow(Carbon::create(2016, 1, 1, 12, 15, 00, 'UTC'));

        $heartbeat->pinged();

        $this->assertSame(Heartbeat::OK, $heartbeat->status);
        $this->assertSame(0, $heartbeat->missed);
        $this->assertSameTimestamp('2016-01-01 12:15:00', $heartbeat->last_activity);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastCreatedEvent()
    {
        $this->assertBroadcastCreatedEvent(Heartbeat::class);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastUpdatedEvent()
    {
        $this->assertBroadcastUpdatedEvent(Heartbeat::class, [
            'status' => Heartbeat::MISSING,
        ], [
            'status' => Heartbeat::OK,
        ]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastTrashedEvent()
    {
        $this->assertBroadcastTrashedEvent(Heartbeat::class);
    }
}
