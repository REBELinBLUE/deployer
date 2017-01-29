<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Notification;
use Mockery as m;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Events\UrlDown;
use REBELinBLUE\Deployer\Events\UrlUp;
use REBELinBLUE\Deployer\Listeners\SendCheckUrlNotification;
use REBELinBLUE\Deployer\Notifications\Configurable\UrlDown as UrlFailed;
use REBELinBLUE\Deployer\Notifications\Configurable\UrlRecovered;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Listeners\SendCheckUrlNotification
 */
class SendCheckUrlNotificationTest extends TestCase
{
    /**
     * @dataProvider getTestNotificationData
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

        $url = m::mock(CheckUrl::class);
        $url->shouldReceive('isHealthy')->once()->andReturn($isHealthy);

        if (!$isHealthy) {
            $url->shouldReceive('getAttribute')->atLeast()->once()->with('missed')->andReturn($missed);
        }

        $url->shouldReceive('getAttribute')->atLeast()->once()->with('project')->andReturn($project);

        Notification::fake();

        $listener = new SendCheckUrlNotification();
        $listener->handle(new $event($url));

        Notification::assertSentTo($channel, $notification);
    }

    public function getTestNotificationData()
    {
        return [
            [UrlUp::class, UrlRecovered::class, true, 0, 'on_link_recovered'],
            [UrlDown::class, UrlFailed::class, false, 1, 'on_link_down'],
            [UrlDown::class, UrlFailed::class, false, 2, 'on_link_still_down'],
        ];
    }
}
