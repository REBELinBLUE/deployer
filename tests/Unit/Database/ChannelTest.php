<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Database;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Notifications\System\NewTestNotification;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\BroadcastChanges;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Channel
 * @group slow
 */
class ChannelTest extends TestCase
{
    use DatabaseMigrations, BroadcastChanges;

    /**
     * @covers ::boot
     */
    public function testBoot()
    {
        Notification::fake();

        /** @var Channel $channel */
        $channel = factory(Channel::class)->make();
        $channel->save();

        Notification::assertSentTo($channel, NewTestNotification::class);
    }

    /**
     * @dataProvider provideTypes
     * @covers ::routeNotificationForMail
     */
    public function testRouteNotificationForMail($type, $expected = null, array $config = [])
    {
        if ($type === Channel::EMAIL) {
            $expected = 'admin@example.com';
            $config   = ['email' => $expected];
        }

        /** @var Channel $channel */
        $channel = factory(Channel::class)->make([
            'type'   => $type,
            'config' => $config,
        ]);
        $actual = $channel->routeNotificationForMail();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::routeNotificationForSlack
     * @dataProvider provideTypes
     */
    public function testRouteNotificationForSlack($type, $expected = null, array $config = [])
    {
        if ($type === Channel::SLACK) {
            $expected = 'http://slack.example.com/webhook';
            $config   = ['webhook' => $expected];
        }

        /** @var Channel $channel */
        $channel = factory(Channel::class)->make([
            'type'   => $type,
            'config' => $config,
        ]);
        $actual = $channel->routeNotificationForSlack();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::routeNotificationForWebhook
     * @dataProvider provideTypes
     */
    public function testRouteNotificationForWebhook($type, $expected = null, array $config = [])
    {
        if ($type === Channel::WEBHOOK) {
            $expected = 'http://www.example.com/webhook';
            $config   = ['url' => $expected];
        }

        /** @var Channel $channel */
        $channel = factory(Channel::class)->make([
            'type'   => $type,
            'config' => $config,
        ]);
        $actual = $channel->routeNotificationForWebhook();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::routeNotificationForTwilio
     * @dataProvider provideTypes
     */
    public function testRouteNotificationForTwilio($type, $expected = null, array $config = [])
    {
        if ($type === Channel::TWILIO) {
            $expected = '+4477012345671';
            $config   = ['telephone' => $expected];
        }

        /** @var Channel $channel */
        $channel = factory(Channel::class)->make([
            'type'   => $type,
            'config' => $config,
        ]);
        $actual = $channel->routeNotificationForTwilio();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::routeNotificationForHipchat
     * @dataProvider provideTypes
     */
    public function testRouteNotificationForHipchat($type, $expected = null, array $config = [])
    {
        if ($type === Channel::HIPCHAT) {
            $expected = '#channel';
            $config   = ['room' => $expected];
        }

        /** @var Channel $channel */
        $channel = factory(Channel::class)->make([
            'type'   => $type,
            'config' => $config,
        ]);
        $actual = $channel->routeNotificationForHipchat();

        $this->assertSame($expected, $actual);
    }

    public function provideTypes()
    {
        return array_chunk($this->fixture('Channel')['types'], 1);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastCreatedEvent()
    {
        $this->assertBroadcastCreatedEvent(Channel::class);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastUpdatedEvent()
    {
        $this->assertBroadcastUpdatedEvent(Channel::class, [
            'on_deployment_success' => false,
        ], [
            'on_deployment_success' => true,
        ]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\BroadcastChanges::bootBroadcastChanges
     */
    public function testBroadcastTrashedEvent()
    {
        $this->assertBroadcastTrashedEvent(Channel::class);
    }
}
