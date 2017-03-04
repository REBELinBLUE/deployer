<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Notifications\Configurable;

use Carbon\Carbon;
use Mockery as m;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Notifications\Configurable\UrlDown;
use REBELinBLUE\Deployer\Notifications\Notification;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Notifications\Configurable\UrlDown
 */
class UrlDownTest extends UrlChangedTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsNotification()
    {
        $url = m::mock(CheckUrl::class);

        $notification = new UrlDown($url, $this->translator);

        $this->assertInstanceOf(Notification::class, $notification);
    }

    /**
     * @covers ::__construct
     * @covers ::toTwilio
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\UrlChanged::buildTwilioMessage
     */
    public function testToTwilio()
    {
        $expectedDateString = 'no-date';

        $this->translator->shouldReceive('trans')->once()->with('app.never')->andReturn($expectedDateString);

        $this->toTwilio(UrlDown::class, 'checkUrls.never_sms_message', null, $expectedDateString);
    }

    /**
     * @covers ::__construct
     * @covers ::toTwilio
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\UrlChanged::buildTwilioMessage
     */
    public function testToTwilioWithLastSeenDate()
    {
        $date = Carbon::create(2015, 1, 1, 12, 00, 00, 'UTC');
        Carbon::setTestNow(Carbon::create(2015, 1, 1, 12, 15, 00, 'UTC'));

        $this->toTwilio(UrlDown::class, 'checkUrls.down_sms_message', $date, '15 minutes ago');
    }

    /**
     * @covers ::__construct
     * @covers ::toWebhook
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\UrlChanged::buildWebhookMessage
     */
    public function testToWebhook()
    {
        $this->toWebhook(UrlDown::class, 'missing', 'link_down', 10);
    }

    /**
     * @covers ::__construct
     * @covers ::toMail
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\UrlChanged::buildMailMessage
     */
    public function testToMail()
    {
        $expectedDateString = 'no-date';

        $this->translator->shouldReceive('trans')->once()->with('app.never')->andReturn($expectedDateString);

        $this->toMail(
            UrlDown::class,
            'checkUrls.down_subject',
            'checkUrls.down_message',
            'error',
            null,
            $expectedDateString
        );
    }

    /**
     * @covers ::__construct
     * @covers ::toMail
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\UrlChanged::buildMailMessage
     */
    public function testToMailWithLastSeenDate()
    {
        $date = Carbon::create(2015, 1, 1, 12, 00, 00, 'UTC');
        Carbon::setTestNow(Carbon::create(2015, 1, 1, 12, 15, 00, 'UTC'));

        $this->toMail(
            UrlDown::class,
            'checkUrls.down_subject',
            'checkUrls.down_message',
            'error',
            $date,
            '15 minutes ago'
        );
    }

    /**
     * @covers ::__construct
     * @covers ::toSlack
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\UrlChanged::buildSlackMessage
     */
    public function testToSlack()
    {
        $expectedDateString = 'no-date';

        $this->translator->shouldReceive('trans')->once()->with('app.never')->andReturn($expectedDateString);

        $this->toSlack(
            UrlDown::class,
            'checkUrls.down_message',
            'error',
            null,
            $expectedDateString
        );
    }

    /**
     * @covers ::__construct
     * @covers ::toSlack
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\UrlChanged::buildSlackMessage
     */
    public function testToSlackWithLastSeenDate()
    {
        $date = Carbon::create(2015, 1, 1, 12, 00, 00, 'UTC');
        Carbon::setTestNow(Carbon::create(2015, 1, 1, 12, 15, 00, 'UTC'));

        $this->toSlack(
            UrlDown::class,
            'checkUrls.down_message',
            'error',
            $date,
            '15 minutes ago'
        );
    }

    /**
     * @covers ::__construct
     * @covers ::toHipchat
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\UrlChanged::buildHipchatMessage
     */
    public function testToHipchat()
    {
        $expectedDateString = 'no-date';

        $this->translator->shouldReceive('trans')->once()->with('app.never')->andReturn($expectedDateString);

        $this->toHipchat(
            UrlDown::class,
            'checkUrls.down_message',
            'error',
            null,
            $expectedDateString
        );
    }

    /**
     * @covers ::__construct
     * @covers ::toHipchat
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\UrlChanged::buildHipchatMessage
     */
    public function testToHipchatWithLastSeenDate()
    {
        $date = Carbon::create(2015, 1, 1, 12, 00, 00, 'UTC');
        Carbon::setTestNow(Carbon::create(2015, 1, 1, 12, 15, 00, 'UTC'));

        $this->toHipchat(
            UrlDown::class,
            'checkUrls.down_message',
            'error',
            $date,
            '15 minutes ago'
        );
    }
}
