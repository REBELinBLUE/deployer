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
     *
     * @param string $event
     */
    public function testScopeForEvent(string $event)
    {
        $builder = m::mock(Builder::class);
        $builder->shouldReceive('where')->once()->with('on_' . $event, '=', true)->andReturnSelf();

        $channel = new Channel();
        $channel->scopeForEvent($builder, $event);
    }

    public function provideEvents(): array
    {
        return array_chunk($this->fixture('Channel')['events'], 1);
    }

    /**
     * @dataProvider provideTypes
     * @covers ::routeNotificationForMail
     *
     * @param string      $type
     * @param string|null $expected
     * @param array       $config
     */
    public function testRouteNotificationForMail(string $type, ?string $expected = null, array $config = [])
    {
        if ($type === Channel::EMAIL) {
            $expected = 'admin@example.com';
            $config   = ['email' => $expected];
        }

        /** @var Channel $channel */
        $channel = factory(Channel::class)->make([
            'type'       => $type,
            'config'     => $config,
            'project_id' => 1,
        ]);
        $actual = $channel->routeNotificationForMail();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::routeNotificationForSlack
     * @dataProvider provideTypes
     *
     * @param string      $type
     * @param string|null $expected
     * @param array       $config
     */
    public function testRouteNotificationForSlack(string $type, ?string $expected = null, array $config = [])
    {
        if ($type === Channel::SLACK) {
            $expected = 'http://slack.example.com/webhook';
            $config   = ['webhook' => $expected];
        }

        /** @var Channel $channel */
        $channel = factory(Channel::class)->make([
            'type'       => $type,
            'config'     => $config,
            'project_id' => 1,
        ]);
        $actual = $channel->routeNotificationForSlack();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::routeNotificationForWebhook
     * @dataProvider provideTypes
     *
     * @param string      $type
     * @param string|null $expected
     * @param array       $config
     */
    public function testRouteNotificationForWebhook(string $type, ?string $expected = null, array $config = [])
    {
        if ($type === Channel::WEBHOOK) {
            $expected = 'http://www.example.com/webhook';
            $config   = ['url' => $expected];
        }

        /** @var Channel $channel */
        $channel = factory(Channel::class)->make([
            'type'       => $type,
            'config'     => $config,
            'project_id' => 1,
        ]);
        $actual = $channel->routeNotificationForWebhook();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::routeNotificationForTwilio
     * @dataProvider provideTypes
     *
     * @param string      $type
     * @param string|null $expected
     * @param array       $config
     */
    public function testRouteNotificationForTwilio(string $type, ?string $expected = null, array $config = [])
    {
        if ($type === Channel::TWILIO) {
            $expected = '+4477012345671';
            $config   = ['telephone' => $expected];
        }

        /** @var Channel $channel */
        $channel = factory(Channel::class)->make([
            'type'       => $type,
            'config'     => $config,
            'project_id' => 1,
        ]);
        $actual = $channel->routeNotificationForTwilio();

        $this->assertSame($expected, $actual);
    }

    public function provideTypes(): array
    {
        return array_chunk($this->fixture('Channel')['types'], 1);
    }
}
