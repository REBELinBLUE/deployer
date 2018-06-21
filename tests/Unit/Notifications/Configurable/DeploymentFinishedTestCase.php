<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Notifications\Configurable;

use Carbon\Carbon;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Routing\UrlGenerator;
use Mockery as m;
use NotificationChannels\HipChat\CardAttribute;
use NotificationChannels\HipChat\CardAttributeStyles;
use NotificationChannels\HipChat\CardFormats;
use NotificationChannels\HipChat\CardStyles;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;

abstract class DeploymentFinishedTestCase extends TestCase
{
    protected $translator;

    public function setUp()
    {
        parent::setUp();

        $this->translator = m::mock(Translator::class);
    }

    protected function toTwilio($class, $translation)
    {
        $expectedMessage = 'the-message';
        $expectedProject = 'a-project-name';
        $expectedId      = 50;

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->atLeast()->once()->with('name')->andReturn($expectedProject);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->once()->with('id')->andReturn($expectedId);

        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with($translation, [
                             'id'      => $expectedId,
                             'project' => $expectedProject,
                         ])
                         ->andReturn($expectedMessage);

        $notification = new $class($project, $deployment, $this->translator);
        $twilio       = $notification->toTwilio();

        $this->assertSame($expectedMessage, $twilio->content);
    }

    protected function toWebhook($class, $expectedStatus, $expectedEvent)
    {
        $expectedId           = 1;
        $expectedDeploymentId = 431;
        $expectedProjectId    = 53;
        $expectedCommitter    = 'some name';
        $expectedProject      = 'project name';
        $expectedDeployer     = 'deployer name';
        $expectedActionUrl    = 'http://deployment.example.com/';

        $expectedData = [
            'id'              => $expectedId,
            'branch'          => 'a branch',
            'started_at'      => Carbon::create(2015, 1, 1, 12, 00, 00, 'UTC'),
            'finished_at'     => Carbon::create(2015, 1, 1, 12, 30, 00, 'UTC'),
            'commit'          => '12345abcd',
            'source'          => 'Github',
            'reason'          => 'reason',
        ];

        $expected = array_merge($expectedData, [
            'project'       => $expectedProject,
            'committed_by'  => $expectedCommitter,
            'started_by'    => $expectedDeployer,
            'status'        => $expectedStatus,
            'url'           => $expectedActionUrl,
        ]);

        $project = m::mock(Project::class);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('attributesToArray')->atLeast()->once()->andReturn($expectedData);
        $deployment->shouldReceive('getAttribute')->once()->with('id')->andReturn($expectedDeploymentId);
        $deployment->shouldReceive('getAttribute')->once()->with('project_name')->andReturn($expectedProject);
        $deployment->shouldReceive('getAttribute')->once()->with('committer')->andReturn($expectedCommitter);
        $deployment->shouldReceive('getAttribute')->once()->with('deployer_name')->andReturn($expectedDeployer);

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->once()->with('id')->andReturn($expectedId);
        $channel->shouldReceive('getAttribute')->once()->with('project_id')->andReturn($expectedProjectId);

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('deployments', ['id' => $expectedDeploymentId], true)
             ->andReturn($expectedActionUrl);

        $this->app->instance('url', $mock);

        $notification = new $class($project, $deployment, $this->translator);
        $webhook      = $notification->toWebhook($channel);
        $actual       = $webhook->toArray();

        $this->assertSame($expected, $actual['data']);

        $this->assertSame(3, count($actual['headers']));
        $this->assertSame($expectedProjectId, $actual['headers']['X-Deployer-Project-Id']);
        $this->assertSame($expectedId, $actual['headers']['X-Deployer-Notification-Id']);
        $this->assertSame($expectedEvent, $actual['headers']['X-Deployer-Event']);
    }

    protected function toMail($class, $subject, $message, $level, $withReason = false)
    {
        $startedDate  = Carbon::create(2016, 1, 1, 12, 00, 00, 'UTC');
        $finishedDate = Carbon::create(2016, 1, 1, 13, 25, 00, 'UTC');

        $expectedName        = 'a-name';
        $expectedSubject     = 'the-email-subject';
        $expectedMessage     = 'the email message';
        $expectedId          = 53;
        $expectedProjectName = 'a-project-name';
        $expectedBranchName  = 'master';
        $expectedActionUrl   = 'http://deployment.example.com/';
        $expectedActionText  = 'the action text';
        $expectedCommitter   = 'a committer name';
        $expectedCommit      = '1234abcd';
        $expectedReason      = ($withReason ? 'A deployment reason' : '');

        $expectedTable = [
            'project'             => $expectedProjectName,
            'deployed_branch'     => $expectedBranchName,
            'started_at'          => $startedDate,
            'finished_at'         => $finishedDate,
            'last_committer'      => $expectedCommitter,
            'last_commit'         => $expectedCommit,
        ];

        $this->translator->shouldReceive('trans')->once()->with($message)->andReturn($expectedMessage);
        $this->translator->shouldReceive('trans')->once()->with($subject)->andReturn($expectedSubject);
        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with('notifications.project_name')
                         ->andReturn('project');
        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with('notifications.deployed_branch')
                         ->andReturn('deployed_branch');
        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with('notifications.started_at')
                         ->andReturn('started_at');
        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with('notifications.finished_at')
                         ->andReturn('finished_at');
        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with('notifications.last_committer')
                         ->andReturn('last_committer');
        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with('notifications.last_commit')
                         ->andReturn('last_commit');
        $this->translator->shouldReceive('trans')
                         ->once()
                         ->with('notifications.deployment_details')
                         ->andReturn($expectedActionText);

        if ($withReason) {
            $this->translator->shouldReceive('trans')
                             ->once()
                             ->with('notifications.reason', ['reason' => $expectedReason])
                             ->andReturn($expectedReason);
        }

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedProjectName);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->once()->with('id')->andReturn($expectedId);
        $deployment->shouldReceive('getAttribute')->once()->with('branch')->andReturn($expectedBranchName);
        $deployment->shouldReceive('getAttribute')->once()->with('started_at')->andReturn($startedDate);
        $deployment->shouldReceive('getAttribute')->once()->with('finished_at')->andReturn($finishedDate);
        $deployment->shouldReceive('getAttribute')->once()->with('committer')->andReturn($expectedCommitter);
        $deployment->shouldReceive('getAttribute')->once()->with('short_commit')->andReturn($expectedCommit);

        $deployment->shouldReceive('offsetExists')->atLeast()->once()->with('reason')->andReturn($withReason);
        if ($withReason) {
            $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('reason')->andReturn($expectedReason);
        }

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedName);

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('deployments', ['id' => $expectedId], true)
             ->andReturn($expectedActionUrl);

        $this->app->instance('url', $mock);

        $notification = new $class($project, $deployment, $this->translator);
        $mail         = $notification->toMail($channel);
        $actual       = $mail->toArray();

        $this->assertSame($expectedSubject, $actual['subject']);
        $this->assertSame(1, count($actual['introLines']));
        $this->assertSame($expectedMessage, $actual['introLines'][0]);

        if ($withReason) {
            $this->assertSame(1, count($actual['outroLines']));
            $this->assertSame($expectedReason, $actual['outroLines'][0]);
        }

        $this->assertSame($expectedActionUrl, $actual['actionUrl']);
        $this->assertSame($expectedActionText, $actual['actionText']);

        $this->assertSame($level, $actual['level']);

        $this->assertArrayHasKey('name', $mail->viewData);
        $this->assertArrayHasKey('table', $mail->viewData);
        $this->assertSame($expectedName, $mail->viewData['name']);
        $this->assertSame($expectedTable, $mail->viewData['table']);
    }

    protected function toSlack($class, $message, $level, $hasCommitUrl = true)
    {
        $expectedTimestamp   = Carbon::create(2017, 1, 1, 12, 0, 0, 'UTC');
        $expectedId          = 53;
        $expectedProjectName = 'a-project-name';
        $expectedProjectId   = 143;
        $expectedBranchName  = 'master';
        $expectedAppName     = 'the app name';
        $expectedProjectUrl  = 'http://project.example.com/';
        $expectedUrl         = 'http://deployment.example.com/';
        $expectedCommitter   = 'a committer name';
        $expectedCommit      = '1234abcd';
        $expectedCommitUrl   = 'http://git.example.com/';
        $expectedIcon        = 'an-icon';
        $expectedChannel     = '#channel';
        $expectedMessage     = 'the slack message #' . $expectedId;
        $expectedContent     = 'the slack message <' . $expectedUrl . '|#' . $expectedId . '>';

        $expectedFields = [
            'project'   => '<' . $expectedProjectUrl . '|' . $expectedProjectName . '>',
            'commit'    => '<' . $expectedCommitUrl . '|' . $expectedCommit . '>',
            'committer' => $expectedCommitter,
            'branch'    => $expectedBranchName,
        ];

        if (!$hasCommitUrl) {
            $expectedFields['commit'] = $expectedCommit;
            $expectedCommitUrl        = false;
        }

        $this->translator->shouldReceive('trans')->once()->with('notifications.project')->andReturn('project');
        $this->translator->shouldReceive('trans')->once()->with('notifications.commit')->andReturn('commit');
        $this->translator->shouldReceive('trans')->once()->with('notifications.committer')->andReturn('committer');
        $this->translator->shouldReceive('trans')->once()->with('notifications.branch')->andReturn('branch');
        $this->translator->shouldReceive('trans')->once()->with('app.name')->andReturn($expectedAppName);
        $this->translator->shouldReceive('trans')->once()->with($message)->andReturn('the slack message %s');

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedProjectName);
        $project->shouldReceive('getAttribute')->atLeast()->once()->with('id')->andReturn($expectedProjectId);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('id')->andReturn($expectedId);
        $deployment->shouldReceive('getAttribute')->once()->with('short_commit')->andReturn($expectedCommit);
        $deployment->shouldReceive('getAttribute')->once()->with('committer')->andReturn($expectedCommitter);
        $deployment->shouldReceive('getAttribute')->once()->with('branch')->andReturn($expectedBranchName);

        $deployment->shouldReceive('getAttribute')
                   ->atLeast()
                   ->once()
                   ->with('commit_url')
                   ->andReturn($expectedCommitUrl);

        $deployment->shouldReceive('getAttribute')->once()->with('finished_at')->andReturn($expectedTimestamp);

        $config = (object) ['icon' => $expectedIcon, 'channel' => $expectedChannel];

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->atLeast()->once()->with('config')->andReturn($config);

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('projects', ['id' => $expectedProjectId], true)
             ->andReturn($expectedProjectUrl);

        $mock->shouldReceive('route')
             ->with('deployments', ['id' => $expectedId], true)
             ->andReturn($expectedUrl);

        $this->app->instance('url', $mock);

        $notification = new $class($project, $deployment, $this->translator);
        $slack        = $notification->toSlack($channel);

        $this->assertSame($expectedIcon, $slack->icon);
        $this->assertSame($expectedChannel, $slack->channel);
        $this->assertSame($level, $slack->level);

        $this->assertSame(1, count($slack->attachments));

        $attachment = $slack->attachments[0];

        $this->assertSame($expectedContent, $attachment->content);
        $this->assertSame($expectedMessage, $attachment->fallback);
        $this->assertSame($expectedAppName, $attachment->footer);
        $this->assertSame($expectedTimestamp->timestamp, $attachment->timestamp);

        $this->assertSame($expectedFields, $attachment->fields);
    }

    protected function toHipchat($class, $message, $level)
    {
        $expectedId            = 53;
        $expectedProjectName   = 'a-project-name';
        $expectedProjectId     = 143;
        $expectedBranchName    = 'master';
        $expectedProjectUrl    = 'http://project.example.com/';
        $expectedDeploymentUrl = 'http://deployment.example.com/';
        $expectedCommitter     = 'a committer name';
        $expectedCommit        = '1234abcd';
        $expectedCommitUrl     = 'http://git.example.com/';
        $expectedRoom          = '#channel';
        $expectedMessage       = 'hipchat message <a href="' . $expectedDeploymentUrl . '">#' . $expectedId . '</a>';
        $expectedTitle         = 'hipchat message #' . $expectedId;

        $this->translator->shouldReceive('trans')->once()->with('notifications.project')->andReturn('project');
        $this->translator->shouldReceive('trans')->once()->with('notifications.commit')->andReturn('commit');
        $this->translator->shouldReceive('trans')->once()->with('notifications.committer')->andReturn('committer');
        $this->translator->shouldReceive('trans')->once()->with('notifications.branch')->andReturn('branch');
        $this->translator->shouldReceive('trans')->once()->with($message)->andReturn('hipchat message %s');

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('name')->andReturn($expectedProjectName);
        $project->shouldReceive('getAttribute')->once()->with('id')->andReturn($expectedProjectId);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('id')->andReturn($expectedId);
        $deployment->shouldReceive('getAttribute')->once()->with('short_commit')->andReturn($expectedCommit);
        $deployment->shouldReceive('getAttribute')->once()->with('committer')->andReturn($expectedCommitter);
        $deployment->shouldReceive('getAttribute')->once()->with('branch')->andReturn($expectedBranchName);
        $deployment->shouldReceive('getAttribute')->once()->with('commit_url')->andReturn($expectedCommitUrl);

        $config = (object) ['room' => $expectedRoom];

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('getAttribute')->atLeast()->once()->with('config')->andReturn($config);

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('projects', ['id' => $expectedProjectId], true)
             ->andReturn($expectedProjectUrl);

        $mock->shouldReceive('route')
             ->with('deployments', ['id' => $expectedId], true)
             ->andReturn($expectedDeploymentUrl);

        $this->app->instance('url', $mock);

        $notification = new $class($project, $deployment, $this->translator);
        $hipchat      = $notification->toHipchat($channel);

        $this->assertSame($expectedRoom, $hipchat->room);
        $this->assertTrue($hipchat->notify);
        $this->assertSame($level, $hipchat->level);
        $this->assertSame($expectedMessage, $hipchat->content);

        $card = $hipchat->card;

        $this->assertSame($expectedTitle, $card->title);
        $this->assertSame(CardStyles::APPLICATION, $card->style);
        $this->assertSame(CardFormats::MEDIUM, $card->cardFormat);
        $this->assertSame($expectedDeploymentUrl, $card->url);

        $attributes = $card->attributes;

        $this->assertSame(4, count($attributes));
        $this->assertCardIsExpected($attributes[0], $expectedProjectName, 'project', $expectedProjectUrl);
        $this->assertCardIsExpected($attributes[1], $expectedCommit, 'commit', $expectedCommitUrl);
        $this->assertCardIsExpected($attributes[2], $expectedCommitter, 'committer');
        $this->assertCardIsExpected($attributes[3], $expectedBranchName, 'branch');

        $this->assertSame(CardAttributeStyles::GENERAL, $attributes[3]->style);
    }

    private function assertCardIsExpected(CardAttribute $card, $expectedValue, $expectedLabel, $expectedUrl = null)
    {
        $this->assertSame($expectedValue, $card->value);
        $this->assertSame($expectedLabel, $card->label);
        $this->assertSame($expectedUrl, $card->url);
    }
}
