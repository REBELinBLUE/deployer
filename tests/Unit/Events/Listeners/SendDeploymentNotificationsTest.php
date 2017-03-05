<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events\Listeners;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\Notification;
use Mockery as m;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Events\DeploymentFinished;
use REBELinBLUE\Deployer\Events\Listeners\SendDeploymentNotifications;
use REBELinBLUE\Deployer\Notifications\Configurable\DeploymentFailed;
use REBELinBLUE\Deployer\Notifications\Configurable\DeploymentSucceeded;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\Listeners\SendDeploymentNotifications
 */
class SendDeploymentNotificationsTest extends TestCase
{
    /**
     * @dataProvider provideTestNotificationData
     * @covers ::handle
     */
    public function testHandleSendsNotification(
        $notification,
        $isSuccessful,
        $field
    ) {
        $expected = m::mock(Channel::class);
        $expected->shouldDeferMissing();

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('where')->once()->with($field, true)->andReturn([$expected]);
        $channel->shouldReceive('getKey');

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->atLeast()->once()->with('channels')->andReturn($channel);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('project')->andReturn($project);
        $deployment->shouldReceive('isAborted')->once()->andReturn(false);
        $deployment->shouldReceive('isSuccessful')->once()->andReturn($isSuccessful);

        $translator = m::mock(Translator::class);

        Notification::fake();

        $listener = new SendDeploymentNotifications($translator);
        $listener->handle(new DeploymentFinished($deployment));

        Notification::assertSentTo($channel, $notification);
    }

    public function provideTestNotificationData()
    {
        return $this->fixture('Listeners/SendDeploymentNotifications')['notifications'];
    }

    public function testHandleDoesNotSendNotificationWhenAborted()
    {
        $expected = m::mock(Channel::class);
        $expected->shouldDeferMissing();

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('where')->never()->with('on_deployment_failure', true)->andReturn([$expected]);
        $channel->shouldReceive('getKey');

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->never()->with('channels')->andReturn($channel);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('project')->andReturn($project);
        $deployment->shouldReceive('isAborted')->once()->andReturn(true);
        $deployment->shouldReceive('isSuccessful')->never()->andReturn(false);

        $translator = m::mock(Translator::class);

        Notification::fake();

        $listener = new SendDeploymentNotifications($translator);
        $listener->handle(new DeploymentFinished($deployment));

        Notification::assertNotSentTo($channel, DeploymentFailed::class);
        Notification::assertNotSentTo($channel, DeploymentSucceeded::class);
    }
}
