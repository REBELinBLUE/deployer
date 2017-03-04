<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Notifications\Configurable;

use Carbon\Carbon;
use Mockery as m;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Notifications\Configurable\UrlRecovered;
use REBELinBLUE\Deployer\Notifications\Notification;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Notifications\Configurable\UrlRecovered
 */
class UrlRecoveredTest extends UrlChangedTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsNotification()
    {
        $url = m::mock(CheckUrl::class);

        $notification = new UrlRecovered($url, $this->translator);

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

        $this->toTwilio(UrlRecovered::class, 'checkUrls.recovered_sms_message', null, $expectedDateString);
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

        $this->toTwilio(UrlRecovered::class, 'checkUrls.recovered_sms_message', $date, '15 minutes ago');
    }

    /**
     * @covers ::__construct
     * @covers ::toWebhook
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\UrlChanged::buildWebhookMessage
     */
    public function testToWebhook()
    {
        $this->toWebhook(UrlRecovered::class, 'healthy', 'link_recovered', 0);
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
            UrlRecovered::class,
            'checkUrls.recovered_subject',
            'checkUrls.recovered_message',
            'success',
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
            UrlRecovered::class,
            'checkUrls.recovered_subject',
            'checkUrls.recovered_message',
            'success',
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
            UrlRecovered::class,
            'checkUrls.recovered_message',
            'success',
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
            UrlRecovered::class,
            'checkUrls.recovered_message',
            'success',
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
            UrlRecovered::class,
            'checkUrls.recovered_message',
            'success',
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
            UrlRecovered::class,
            'checkUrls.recovered_message',
            'success',
            $date,
            '15 minutes ago'
        );
    }
}
