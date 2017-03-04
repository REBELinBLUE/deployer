<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Notifications\System;

use Illuminate\Contracts\Translation\Translator;
use Mockery as m;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Notifications\System\NewTestNotification;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Notifications\System\NewTestNotification
 */
class NewTestNotificationTest extends TestCase
{
    private $translator;

    public function setUp()
    {
        parent::setUp();

        $this->translator = m::mock(Translator::class);
    }

    /**
     * @dataProvider provideChannelTypes
     * @covers ::__construct
     * @covers \REBELinBLUE\Deployer\Notifications\Notification::via
     */
    public function testSendVia($type, $expected)
    {
        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->atLeast()->once()->with('type')->andReturn($type);

        $notification = new NewTestNotification($this->translator);

        $this->assertSame([$expected], $notification->via($channel));
    }

    /**
     * @covers ::__construct
     * @covers ::toMail
     */
    public function testToMail()
    {
        $expectedName   = 'Bob Smith';
        $subject        = 'expected subject';
        $line           = 'in line 1 of text';

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedName);

        $this->translator->shouldReceive('trans')->with('notifications.test_subject')->andReturn($subject);
        $this->translator->shouldReceive('trans')->with('notifications.test_message')->andReturn($line);

        $notification = new NewTestNotification($this->translator);
        $mail         = $notification->toMail($channel);
        $actual       = $mail->toArray();

        $this->assertSame($subject, $actual['subject']);
        $this->assertSame(1, count($actual['introLines']));
        $this->assertSame($line, $actual['introLines'][0]);
        $this->assertArrayHasKey('name', $mail->viewData);
        $this->assertSame($expectedName, $mail->viewData['name']);
    }

    public function provideChannelTypes()
    {
        return $this->fixture('Notifications/System/NewTestNotification');
    }

    /**
     * @covers ::__construct
     * @covers ::toSlack
     */
    public function testToSlack()
    {
        $expectedMessage = 'a-test-message';
        $expectedChannel = '#channel';

        $config = (object) ['channel' => $expectedChannel];

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->once()->with('config')->andReturn($config);

        $this->translator->shouldReceive('trans')
                         ->with('notifications.test_slack_message')
                         ->andReturn($expectedMessage);

        $notification = new NewTestNotification($this->translator);
        $slack        = $notification->toSlack($channel);

        $this->assertSame($expectedMessage, $slack->content);
        $this->assertSame($expectedChannel, $slack->channel);
    }

    /**
     * @covers ::__construct
     * @covers ::toTwilio
     */
    public function testToTwilio()
    {
        $expected = 'a-test-message';

        $this->translator->shouldReceive('trans')->with('notifications.test_message')->andReturn($expected);

        $notification = new NewTestNotification($this->translator);
        $twilio       = $notification->toTwilio();

        $this->assertSame($expected, $twilio->content);
    }

    /**
     * @covers ::__construct
     * @covers ::toWebhook
     */
    public function testToWebhook()
    {
        $expected_id       = 2;
        $project           = 10;
        $expected          = 'a-test-message';

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->once()->with('id')->andReturn($expected_id);
        $channel->shouldReceive('getAttribute')->once()->with('project_id')->andReturn($project);

        $this->translator->shouldReceive('trans')->with('notifications.test_message')->andReturn($expected);

        $notification  = new NewTestNotification($this->translator);
        $webhook       = $notification->toWebhook($channel);
        $actual        = $webhook->toArray();

        $this->assertSame(1, count($actual['data']));
        $this->assertArrayHasKey('message', $actual['data']);
        $this->assertSame($expected, $actual['data']['message']);

        $this->assertSame(3, count($actual['headers']));
        $this->assertSame($project, $actual['headers']['X-Deployer-Project-Id']);
        $this->assertSame($expected_id, $actual['headers']['X-Deployer-Notification-Id']);
        $this->assertSame('notification_test', $actual['headers']['X-Deployer-Event']);
    }

    /**
     * @covers ::__construct
     * @covers ::toHipchat
     */
    public function testToHipchat()
    {
        $expectedMessage = 'a-test-message';
        $expectedRoom    = '#channel';

        $config = (object) ['room' => $expectedRoom];

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->once()->with('config')->andReturn($config);

        $this->translator->shouldReceive('trans')
                         ->with('notifications.test_hipchat_message')
                         ->andReturn($expectedMessage);

        $notification  = new NewTestNotification($this->translator);
        $hipchat       = $notification->toHipchat($channel);

        $this->assertSame($expectedMessage, $hipchat->content);
        $this->assertSame('text', $hipchat->format);
        $this->assertSame($expectedRoom, $hipchat->room);
    }
}
