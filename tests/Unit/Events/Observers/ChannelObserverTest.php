<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events\Observers;

use Illuminate\Contracts\Translation\Translator;
use Mockery as m;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Events\Observers\ChannelObserver;
use REBELinBLUE\Deployer\Notifications\System\NewTestNotification;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\Observers\ChannelObserver
 */
class ChannelObserverTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::saved
     */
    public function testSaved()
    {
        $translator = m::mock(Translator::class);

        $channel = m::mock(Channel::class);
        $channel->shouldReceive('notify')->with(m::type(NewTestNotification::class));

        $observer = new ChannelObserver($translator);
        $observer->saved($channel);
    }
}
