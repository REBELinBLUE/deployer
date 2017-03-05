<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Database;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\BroadcastChanges;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\CheckUrl
 * @group slow
 */
class CheckUrlTest extends TestCase
{
    use DatabaseMigrations, BroadcastChanges;

    /**
     * @covers ::offline
     */
    public function testOffline()
    {
        /** @var CheckUrl $url */
        $url = factory(CheckUrl::class)->make([
            'project_id' => 1,
            'status'     => CheckUrl::ONLINE,
            'missed'     => 0,
        ]);

        $url->offline();

        $this->assertSame(CheckUrl::OFFLINE, $url->status);
        $this->assertSame(1, $url->missed);
    }

    /**
     * @covers ::offline
     */
    public function testOfflineIncreasesMissed()
    {
        /** @var CheckUrl $url */
        $url = factory(CheckUrl::class)->make([
            'project_id' => 1,
            'status'     => CheckUrl::OFFLINE,
            'missed'     => 5,
        ]);

        $url->offline();

        $this->assertSame(CheckUrl::OFFLINE, $url->status);
        $this->assertSame(6, $url->missed);
    }

    /**
     * @covers ::online
     */
    public function testOnline()
    {
        /** @var CheckUrl $url */
        $url = factory(CheckUrl::class)->make([
            'project_id' => 1,
            'status'     => CheckUrl::ONLINE,
        ]);

        Carbon::setTestNow(Carbon::create(2016, 1, 1, 12, 15, 00, 'UTC'));

        $url->online();

        $this->assertSame(CheckUrl::ONLINE, $url->status);
        $this->assertSame(0, $url->missed);
        $this->assertSameTimestamp('2016-01-01 12:15:00', $url->last_seen);
    }

    /**
     * @covers ::setUrlAttribute
     */
    public function testSetUrl()
    {
        $link = 'http://www.example.com';

        /** @var CheckUrl $url */
        $url = factory(CheckUrl::class)->create([
            'status'    => CheckUrl::ONLINE,
            'last_seen' => Carbon::create(2016, 1, 1, 12, 15, 00, 'UTC'),
        ]);

        $url->url = $link;

        $this->assertSame(CheckUrl::UNTESTED, $url->status);
        $this->assertNull($url->last_seen);
    }

    /**
     * @covers ::setUrlAttribute
     */
    public function testSetUrlAttributeDoesNotChangeStatusWhenSame()
    {
        $link = 'http://www.example.com';
        $date = Carbon::create(2016, 1, 1, 12, 15, 00, 'UTC');

        /** @var CheckUrl $url */
        $url = factory(CheckUrl::class)->create([
            'url'       => $link,
            'status'    => CheckUrl::ONLINE,
            'last_seen' => $date,
        ]);

        $url->url = $link;

        $this->assertSame(CheckUrl::ONLINE, $url->status);
        $this->assertSameTimestamp('2016-01-01 12:15:00', $url->last_seen);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastCreatedEvent()
    {
        $this->assertBroadcastCreatedEvent(CheckUrl::class);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastUpdatedEvent()
    {
        $this->assertBroadcastUpdatedEvent(CheckUrl::class, [
            'url' => 'http://www.example.com/',
        ], [
            'url' => 'https://www.example.com/',
        ]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastTrashedEvent()
    {
        $this->assertBroadcastTrashedEvent(CheckUrl::class);
    }
}
