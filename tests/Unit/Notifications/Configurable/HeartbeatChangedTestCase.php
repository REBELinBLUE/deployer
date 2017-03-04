<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Notifications\Configurable;

use Carbon\Carbon;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Routing\UrlGenerator;
use Mockery as m;
use NotificationChannels\HipChat\CardAttribute;
use NotificationChannels\HipChat\CardFormats;
use NotificationChannels\HipChat\CardStyles;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;

abstract class HeartbeatChangedTestCase extends TestCase
{
    protected $translator;

    public function setUp()
    {
        parent::setUp();

        $this->translator = m::mock(Translator::class);
    }

    protected function toTwilio($class, $translation, $expectedDate, $expectedDateString)
    {
        $expectedMessage = 'the-message';
        $expectedProject = 'a-project-name';
        $expectedName    = 'a-cronjob-name';

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->atLeast()->once()->with('name')->andReturn($expectedProject);

        $heartbeat = m::mock(Heartbeat::class);
        $heartbeat->shouldReceive('getAttribute')->atLeast()->once()->with('last_activity')->andReturn($expectedDate);
        $heartbeat->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedName);
        $heartbeat->shouldReceive('getAttribute')->once()->with('project')->andReturn($project);

        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with($translation, [
                             'job'     => $expectedName,
                             'project' => $expectedProject,
                             'last'    => $expectedDateString,
                         ])
                         ->andReturn($expectedMessage);

        $notification = new $class($heartbeat, $this->translator);
        $twilio       = $notification->toTwilio();

        $this->assertSame($expectedMessage, $twilio->content);
    }

    protected function toWebhook($class, $expectedStatus, $expectedEvent, $expectedMissed)
    {
        $expectedId        = 1;
        $expectedProjectId = 53;
        $expectedData      = [
            'id'            => $expectedId,
            'name'          => 'a cronjob',
            'missed'        => $expectedMissed,
            'last_activity' => Carbon::create(2015, 1, 1, 12, 00, 00, 'UTC'),
        ];

        $expected = array_merge($expectedData, ['status' => $expectedStatus]);

        $url = m::mock(Heartbeat::class);
        $url->shouldReceive('attributesToArray')->atLeast()->once()->andReturn($expectedData);

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->once()->with('id')->andReturn($expectedId);
        $channel->shouldReceive('getAttribute')->once()->with('project_id')->andReturn($expectedProjectId);

        $notification = new $class($url, $this->translator);
        $webhook      = $notification->toWebhook($channel);
        $actual       = $webhook->toArray();

        $this->assertSame($expected, $actual['data']);

        $this->assertSame(3, count($actual['headers']));
        $this->assertSame($expectedProjectId, $actual['headers']['X-Deployer-Project-Id']);
        $this->assertSame($expectedId, $actual['headers']['X-Deployer-Notification-Id']);
        $this->assertSame($expectedEvent, $actual['headers']['X-Deployer-Event']);
    }

    protected function toMail($class, $subject, $message, $level, $expectedDate, $expectedDateString)
    {
        $expectedProjectId   = 53;
        $expectedProjectName = 'a-project-name';
        $expectedJobName     = 'a-job-name';
        $expectedName        = 'a-name';
        $expectedSubject     = 'the-email-subject';
        $expectedMessage     = 'the email message';
        $expectedActionText  = 'the action text';
        $expectedActionUrl   = 'http://heartbeat.example.com/project';

        $expectedTable = [
            'project'       => $expectedProjectName,
            'last_check_in' => $expectedDateString,
        ];

        $this->translator->shouldReceive('trans')->once()->with($subject)->andReturn($expectedSubject);
        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with('notifications.project_details')
                         ->andReturn($expectedActionText);
        $this->translator->shouldReceive('trans')->once()->with('notifications.project_name')->andReturn('project');
        $this->translator->shouldReceive('trans')->once()->with('heartbeats.last_check_in')->andReturn('last_check_in');

        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with($message, ['job' => $expectedJobName])
                         ->andReturn($expectedMessage);

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedProjectName);

        $heartbeat = m::mock(Heartbeat::class);
        $heartbeat->shouldReceive('getAttribute')->once()->with('project')->andReturn($project);
        $heartbeat->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedJobName);
        $heartbeat->shouldReceive('getAttribute')->atLeast()->once()->with('last_activity')->andReturn($expectedDate);
        $heartbeat->shouldReceive('getAttribute')->once()->with('project_id')->andReturn($expectedProjectId);

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedName);

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('projects', ['id' => $expectedProjectId], true)
             ->andReturn($expectedActionUrl);

        $this->app->instance('url', $mock);

        $notification = new $class($heartbeat, $this->translator);
        $mail         = $notification->toMail($channel);
        $actual       = $mail->toArray();

        $this->assertSame($expectedSubject, $actual['subject']);
        $this->assertSame(1, count($actual['introLines']));
        $this->assertSame($expectedMessage, $actual['introLines'][0]);

        $this->assertSame($expectedActionUrl, $actual['actionUrl']);
        $this->assertSame($expectedActionText, $actual['actionText']);

        $this->assertSame($level, $actual['level']);

        $this->assertArrayHasKey('name', $mail->viewData);
        $this->assertArrayHasKey('table', $mail->viewData);
        $this->assertSame($expectedName, $mail->viewData['name']);
        $this->assertSame($expectedTable, $mail->viewData['table']);
    }

    protected function toSlack($class, $message, $level, $expectedDate, $expectedDateString)
    {
        $expectedProjectId   = 53;
        $expectedTimestamp   = Carbon::create(2015, 1, 1, 12, 0, 0, 'UTC');
        $expectedProjectName = 'a-project-name';
        $expectedJobName     = 'a-cronjob-name';
        $expectedAppName     = 'app-name';
        $expectedMessage     = 'the slack message';
        $expectedIcon        = 'an-icon';
        $expectedChannel     = '#channel';
        $expectedActionUrl   = 'http://url.example.com/project';

        $expectedFields = [
            'project'       => sprintf('<%s|%s>', $expectedActionUrl, $expectedProjectName),
            'last_check_in' => $expectedDateString,
        ];

        $this->translator->shouldReceive('trans')->once()->with('notifications.project')->andReturn('project');
        $this->translator->shouldReceive('trans')->once()->with('heartbeats.last_check_in')->andReturn('last_check_in');
        $this->translator->shouldReceive('trans')->once()->with('app.name')->andReturn($expectedAppName);

        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with($message, ['job' => $expectedJobName])
                         ->andReturn($expectedMessage);

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedProjectName);

        $heartbeat = m::mock(Heartbeat::class);
        $heartbeat->shouldReceive('getAttribute')->once()->with('project')->andReturn($project);
        $heartbeat->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedJobName);
        $heartbeat->shouldReceive('getAttribute')->atLeast()->once()->with('last_activity')->andReturn($expectedDate);
        $heartbeat->shouldReceive('getAttribute')->once()->with('project_id')->andReturn($expectedProjectId);
        $heartbeat->shouldReceive('getAttribute')->once()->with('updated_at')->andReturn($expectedTimestamp);

        $config = (object) ['icon' => $expectedIcon, 'channel' => $expectedChannel];

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->atLeast()->once()->with('config')->andReturn($config);

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('projects', ['id' => $expectedProjectId], true)
             ->andReturn($expectedActionUrl);

        $this->app->instance('url', $mock);

        $notification = new $class($heartbeat, $this->translator);
        $slack        = $notification->toSlack($channel);

        $this->assertSame($expectedIcon, $slack->icon);
        $this->assertSame($expectedChannel, $slack->channel);
        $this->assertSame($level, $slack->level);

        $this->assertSame(1, count($slack->attachments));

        $attachment = $slack->attachments[0];
        $this->assertSame($expectedMessage, $attachment->content);
        $this->assertSame($expectedMessage, $attachment->fallback);
        $this->assertSame($expectedAppName, $attachment->footer);
        $this->assertSame($expectedTimestamp->timestamp, $attachment->timestamp);

        $this->assertSame($expectedFields, $attachment->fields);
    }

    protected function toHipchat($class, $message, $level, $expectedDate, $expectedDateString)
    {
        $expectedProjectId   = 53;
        $expectedProjectName = 'a-project-name';
        $expectedJobName     = 'a-job-name';
        $expectedMessage     = 'the hipchat message';
        $expectedRoom        = '#channel';
        $expectedActionUrl   = 'http://url.example.com/project';

        $this->translator->shouldReceive('trans')->once()->with('notifications.project')->andReturn('project');
        $this->translator->shouldReceive('trans')->once()->with('heartbeats.last_check_in')->andReturn('last_check_in');

        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with($message, ['job' => $expectedJobName])
                         ->andReturn($expectedMessage);

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedProjectName);

        $heartbeat = m::mock(Heartbeat::class);
        $heartbeat->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedJobName);
        $heartbeat->shouldReceive('getAttribute')->atLeast()->once()->with('project_id')->andReturn($expectedProjectId);
        $heartbeat->shouldReceive('getAttribute')->once()->with('project')->andReturn($project);
        $heartbeat->shouldReceive('getAttribute')->atLeast()->once()->with('last_activity')->andReturn($expectedDate);

        $config = (object) ['room' => $expectedRoom];

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->atLeast()->once()->with('config')->andReturn($config);

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('projects', ['id' => $expectedProjectId], true)
             ->andReturn($expectedActionUrl);

        $this->app->instance('url', $mock);

        $notification = new $class($heartbeat, $this->translator);
        $hipchat      = $notification->toHipchat($channel);

        $this->assertSame($expectedRoom, $hipchat->room);
        $this->assertTrue($hipchat->notify);
        $this->assertSame($level, $hipchat->level);
        $this->assertSame($expectedMessage, $hipchat->content);

        $card = $hipchat->card;

        $this->assertSame($expectedMessage, $card->title);
        $this->assertSame(CardStyles::APPLICATION, $card->style);
        $this->assertSame(CardFormats::MEDIUM, $card->cardFormat);
        $this->assertSame($expectedActionUrl, $card->url);

        $attributes = $card->attributes;

        $this->assertSame(2, count($attributes));
        $this->assertCardIsExpected($attributes[0], $expectedProjectName, 'project', $expectedActionUrl);
        $this->assertCardIsExpected($attributes[1], $expectedDateString, 'last_check_in');
    }

    private function assertCardIsExpected(CardAttribute $card, $expectedValue, $expectedLabel, $expectedUrl = null)
    {
        $this->assertSame($expectedValue, $card->value);
        $this->assertSame($expectedLabel, $card->label);
        $this->assertSame($expectedUrl, $card->url);
    }
}
