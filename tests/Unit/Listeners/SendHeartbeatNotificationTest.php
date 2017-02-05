<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Notification;
use Mockery as m;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Listeners\SendHeartbeatNotification;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Listeners\SendHeartbeatNotification
 */
class SendHeartbeatNotificationTest extends TestCase
{
    /**
     * @dataProvider provideTestNotificationData
     * @covers ::handle
     */
    public function testHandleSendsNotification(
        $event,
        $notification,
        $isHealthy,
        $missed,
        $field
    ) {
        $expected = m::mock(Channel::class);
        $expected->shouldDeferMissing();

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('where')->once()->with($field, true)->andReturn([$expected]);
        $channel->shouldReceive('getKey');

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->atLeast()->once()->with('channels')->andReturn($channel);

        $heartbeat = m::mock(Heartbeat::class);
        $heartbeat->shouldReceive('isHealthy')->once()->andReturn($isHealthy);

        if (!$isHealthy) {
            $heartbeat->shouldReceive('getAttribute')->atLeast()->once()->with('missed')->andReturn($missed);
        }

        $heartbeat->shouldReceive('getAttribute')->atLeast()->once()->with('project')->andReturn($project);

        Notification::fake();

        $listener = new SendHeartbeatNotification();
        $listener->handle(new $event($heartbeat));

        Notification::assertSentTo($channel, $notification);
    }

    public function provideTestNotificationData()
    {
        return $this->fixture('Listeners/SendSignupEmail')['notifications'];
    }
}
