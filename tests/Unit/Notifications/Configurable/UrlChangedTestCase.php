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
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;

abstract class UrlChangedTestCase extends TestCase
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
        $expectedName    = 'a-link-name';

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedProject);

        $url = m::mock(CheckUrl::class);
        $url->shouldReceive('getAttribute')->atLeast()->once()->with('last_seen')->andReturn($expectedDate);
        $url->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedName);
        $url->shouldReceive('getAttribute')->once()->with('project')->andReturn($project);

        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with($translation, [
                             'link'    => $expectedName,
                             'project' => $expectedProject,
                             'last'    => $expectedDateString,
                         ])
                         ->andReturn($expectedMessage);

        $notification = new $class($url, $this->translator);
        $twilio       = $notification->toTwilio();

        $this->assertSame($expectedMessage, $twilio->content);
    }

    protected function toWebhook($class, $expectedStatus, $expectedEvent, $expectedMissed)
    {
        $expectedId        = 1;
        $expectedProjectId = 53;
        $expectedData      = [
            'id'        => $expectedId,
            'name'      => 'a link',
            'missed'    => $expectedMissed,
            'last_seen' => Carbon::create(2015, 1, 1, 12, 00, 00, 'UTC'),
        ];

        $expected = array_merge($expectedData, ['status' => $expectedStatus]);

        $url = m::mock(CheckUrl::class);
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
        $expectedUrlName     = 'a-link-name';
        $expectedUrl         = 'http://www.example.com';
        $expectedName        = 'a-name';
        $expectedSubject     = 'the-email-subject';
        $expectedMessage     = 'the email message';
        $expectedActionText  = 'the action text';
        $expectedActionUrl   = 'http://url.example.com/project';

        $expectedTable = [
            'project'       => $expectedProjectName,
            'last_check_in' => $expectedDateString,
            'url'           => $expectedUrl,
        ];

        $this->translator->shouldReceive('trans')->once()->with($subject)->andReturn($expectedSubject);
        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with('notifications.project_details')
                         ->andReturn($expectedActionText);
        $this->translator->shouldReceive('trans')->once()->with('notifications.project_name')->andReturn('project');
        $this->translator->shouldReceive('trans')->once()->with('heartbeats.last_check_in')->andReturn('last_check_in');
        $this->translator->shouldReceive('trans')->once()->with('checkUrls.url')->andReturn('url');

        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with($message, ['link' => $expectedUrlName])
                         ->andReturn($expectedMessage);

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedProjectName);

        $url = m::mock(CheckUrl::class);
        $url->shouldReceive('getAttribute')->once()->with('project')->andReturn($project);
        $url->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedUrlName);
        $url->shouldReceive('getAttribute')->once()->with('url')->andReturn($expectedUrl);
        $url->shouldReceive('getAttribute')->atLeast()->once()->with('last_seen')->andReturn($expectedDate);
        $url->shouldReceive('getAttribute')->once()->with('project_id')->andReturn($expectedProjectId);

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedName);

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('projects', ['id' => $expectedProjectId], true)
             ->andReturn($expectedActionUrl);

        $this->app->instance('url', $mock);

        $notification = new $class($url, $this->translator);
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
        $expectedUrlName     = 'a-link-name';
        $expectedUrl         = 'http://www.example.com';
        $expectedAppName     = 'app-name';
        $expectedMessage     = 'the slack message';
        $expectedIcon        = 'an-icon';
        $expectedChannel     = '#channel';
        $expectedActionUrl   = 'http://url.example.com/project';

        $expectedFields = [
            'project'       => sprintf('<%s|%s>', $expectedActionUrl, $expectedProjectName),
            'last_check_in' => $expectedDateString,
            'url'           => $expectedUrl,
        ];

        $this->translator->shouldReceive('trans')->once()->with('notifications.project')->andReturn('project');
        $this->translator->shouldReceive('trans')->once()->with('checkUrls.last_seen')->andReturn('last_check_in');
        $this->translator->shouldReceive('trans')->once()->with('checkUrls.url')->andReturn('url');
        $this->translator->shouldReceive('trans')->once()->with('app.name')->andReturn($expectedAppName);

        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with($message, ['link' => $expectedUrlName])
                         ->andReturn($expectedMessage);

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedProjectName);

        $url = m::mock(CheckUrl::class);
        $url->shouldReceive('getAttribute')->once()->with('project')->andReturn($project);
        $url->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedUrlName);
        $url->shouldReceive('getAttribute')->once()->with('url')->andReturn($expectedUrl);
        $url->shouldReceive('getAttribute')->atLeast()->once()->with('last_seen')->andReturn($expectedDate);
        $url->shouldReceive('getAttribute')->once()->with('project_id')->andReturn($expectedProjectId);
        $url->shouldReceive('getAttribute')->once()->with('updated_at')->andReturn($expectedTimestamp);

        $config = (object) ['icon' => $expectedIcon, 'channel' => $expectedChannel];

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->atLeast()->once()->with('config')->andReturn($config);

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('projects', ['id' => $expectedProjectId], true)
             ->andReturn($expectedActionUrl);

        $this->app->instance('url', $mock);

        $notification = new $class($url, $this->translator);
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
        $expectedUrlName     = 'a-link-name';
        $expectedUrl         = 'http://www.example.com';
        $expectedMessage     = 'the hipchat message';
        $expectedRoom        = '#channel';
        $expectedActionUrl   = 'http://url.example.com/project';

        $this->translator->shouldReceive('trans')->once()->with('notifications.project')->andReturn('project');
        $this->translator->shouldReceive('trans')->once()->with('checkUrls.last_seen')->andReturn('last_check_in');
        $this->translator->shouldReceive('trans')->once()->with('checkUrls.url')->andReturn('url');

        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with($message, ['link' => $expectedUrlName])
                         ->andReturn($expectedMessage);

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedProjectName);

        $url = m::mock(CheckUrl::class);
        $url->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedUrlName);
        $url->shouldReceive('getAttribute')->atLeast()->once()->with('project_id')->andReturn($expectedProjectId);
        $url->shouldReceive('getAttribute')->once()->with('project')->andReturn($project);
        $url->shouldReceive('getAttribute')->atLeast()->once()->with('last_seen')->andReturn($expectedDate);
        $url->shouldReceive('getAttribute')->atLeast()->once()->with('url')->andReturn($expectedUrl);

        $config = (object) ['room' => $expectedRoom];

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->atLeast()->once()->with('config')->andReturn($config);

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('projects', ['id' => $expectedProjectId], true)
             ->andReturn($expectedActionUrl);

        $this->app->instance('url', $mock);

        $notification = new $class($url, $this->translator);
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

        $this->assertSame(3, count($attributes));
        $this->assertCardIsExpected($attributes[0], $expectedProjectName, 'project', $expectedActionUrl);
        $this->assertCardIsExpected($attributes[1], $expectedDateString, 'last_check_in');
        $this->assertCardIsExpected($attributes[2], $expectedUrl, 'url', $expectedUrl);
    }

    private function assertCardIsExpected(CardAttribute $card, $expectedValue, $expectedLabel, $expectedUrl = null)
    {
        $this->assertSame($expectedValue, $card->value);
        $this->assertSame($expectedLabel, $card->label);
        $this->assertSame($expectedUrl, $card->url);
    }
}
