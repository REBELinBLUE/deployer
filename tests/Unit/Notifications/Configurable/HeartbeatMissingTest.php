<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Notifications\Configurable;

use Carbon\Carbon;
use Mockery as m;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatMissing;
use REBELinBLUE\Deployer\Notifications\Notification;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatMissing
 */
class HeartbeatMissingTest extends HeartbeatChangedTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsNotification()
    {
        $heartbeat = m::mock(Heartbeat::class);

        $notification = new HeartbeatMissing($heartbeat, $this->translator);

        $this->assertInstanceOf(Notification::class, $notification);
    }

    /**
     * @covers ::__construct
     * @covers ::toTwilio
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatChanged::buildTwilioMessage
     */
    public function testToTwilio()
    {
        $expectedDateString = 'no-date';

        $this->translator->shouldReceive('trans')->once()->with('app.never')->andReturn($expectedDateString);

        $this->toTwilio(HeartbeatMissing::class, 'heartbeats.never_sms_message', null, $expectedDateString);
    }

    /**
     * @covers ::__construct
     * @covers ::toTwilio
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatChanged::buildTwilioMessage
     */
    public function testToTwilioWithLastSeenDate()
    {
        $date = Carbon::create(2015, 1, 1, 12, 00, 00, 'UTC');
        Carbon::setTestNow(Carbon::create(2015, 1, 1, 12, 15, 00, 'UTC'));

        $this->toTwilio(HeartbeatMissing::class, 'heartbeats.missing_sms_message', $date, '15 minutes ago');
    }

    /**
     * @covers ::__construct
     * @covers ::toWebhook
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatChanged::buildWebhookMessage
     */
    public function testToWebhook()
    {
        $this->toWebhook(HeartbeatMissing::class, 'missing', 'heartbeat_missing', 10);
    }

    /**
     * @covers ::__construct
     * @covers ::toMail
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatChanged::buildMailMessage
     */
    public function testToMail()
    {
        $expectedDateString = 'no-date';

        $this->translator->shouldReceive('trans')->once()->with('app.never')->andReturn($expectedDateString);

        $this->toMail(
            HeartbeatMissing::class,
            'heartbeats.missing_subject',
            'heartbeats.missing_message',
            'error',
            null,
            $expectedDateString
        );
    }

    /**
     * @covers ::__construct
     * @covers ::toMail
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatChanged::buildMailMessage
     */
    public function testToMailWithLastSeenDate()
    {
        $date = Carbon::create(2015, 1, 1, 12, 00, 00, 'UTC');
        Carbon::setTestNow(Carbon::create(2015, 1, 1, 12, 15, 00, 'UTC'));

        $this->toMail(
            HeartbeatMissing::class,
            'heartbeats.missing_subject',
            'heartbeats.missing_message',
            'error',
            $date,
            '15 minutes ago'
        );
    }

    /**
     * @covers ::__construct
     * @covers ::toSlack
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatChanged::buildSlackMessage
     */
    public function testToSlack()
    {
        $expectedDateString = 'no-date';

        $this->translator->shouldReceive('trans')->once()->with('app.never')->andReturn($expectedDateString);

        $this->toSlack(
            HeartbeatMissing::class,
            'heartbeats.missing_message',
            'error',
            null,
            $expectedDateString
        );
    }

    /**
     * @covers ::__construct
     * @covers ::toSlack
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatChanged::buildSlackMessage
     */
    public function testToSlackWithLastSeenDate()
    {
        $date = Carbon::create(2015, 1, 1, 12, 00, 00, 'UTC');
        Carbon::setTestNow(Carbon::create(2015, 1, 1, 12, 15, 00, 'UTC'));

        $this->toSlack(
            HeartbeatMissing::class,
            'heartbeats.missing_message',
            'error',
            $date,
            '15 minutes ago'
        );
    }

    /**
     * @covers ::__construct
     * @covers ::toHipchat
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatChanged::buildHipchatMessage
     */
    public function testToHipchat()
    {
        $expectedDateString = 'no-date';

        $this->translator->shouldReceive('trans')->once()->with('app.never')->andReturn($expectedDateString);

        $this->toHipchat(
            HeartbeatMissing::class,
            'heartbeats.missing_message',
            'error',
            null,
            $expectedDateString
        );
    }

    /**
     * @covers ::__construct
     * @covers ::toHipchat
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatChanged::buildHipchatMessage
     */
    public function testToHipchatWithLastSeenDate()
    {
        $date = Carbon::create(2015, 1, 1, 12, 00, 00, 'UTC');
        Carbon::setTestNow(Carbon::create(2015, 1, 1, 12, 15, 00, 'UTC'));

        $this->toHipchat(
            HeartbeatMissing::class,
            'heartbeats.missing_message',
            'error',
            $date,
            '15 minutes ago'
        );
    }
}
