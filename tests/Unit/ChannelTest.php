<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mockery as m;
use REBELinBLUE\Deployer\Channel;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Channel
 */
class ChannelTest extends TestCase
{
    use TestsModel;

    /**
     * @covers ::project
     */
    public function testProject()
    {
        $channel = new Channel();
        $actual  = $channel->project();

        $this->assertInstanceOf(BelongsTo::class, $actual);
        $this->assertBelongsTo('project', Channel::class);
    }

    /**
     * @covers ::scopeForEvent
     * @dataProvider provideEvents
     */
    public function testScopeForEvent($event)
    {
        $builder = m::mock(Builder::class);
        $builder->shouldReceive('where')->once()->with('on_' . $event, '=', true)->andReturnSelf();

        $channel = new Channel();
        $channel->scopeForEvent($builder, $event);
    }

    public function provideEvents()
    {
        return array_chunk($this->fixture('Channel')['events'], 1);
    }
}
