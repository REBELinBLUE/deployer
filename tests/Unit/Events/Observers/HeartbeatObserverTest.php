<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events\Observers;

use Mockery as m;
use REBELinBLUE\Deployer\Events\Observers\HeartbeatObserver;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\Observers\HeartbeatObserver
 */
class HeartbeatObserverTest extends TestCase
{
    /**
     * @covers ::creating
     */
    public function testCreating()
    {
        $expected = 'my-fake-token';

        $this->mockTokenGenerator($expected);

        /** @var Heartbeat $heartbeat */
        $heartbeat = factory(Heartbeat::class)->make([
            'project_id' => 1,
        ]);

        $this->assertEmpty($heartbeat->hash);

        $observer = new HeartbeatObserver();
        $observer->creating($heartbeat);

        $this->assertSame($expected, $heartbeat->hash);
    }

    /**
     * @covers ::creating
     */
    public function testBootShouldNotRegenerateHashIfSet()
    {
        $expected = 'my-fake-token';

        $generator = m::mock(TokenGeneratorInterface::class);
        $generator->shouldNotReceive('generateRandom');

        $this->app->instance(TokenGeneratorInterface::class, $generator);

        /** @var Heartbeat $heartbeat */
        $heartbeat = factory(Heartbeat::class)->make([
            'project_id' => 1,
            'hash'       => $expected,
        ]);

        $this->assertSame($expected, $heartbeat->hash);

        $observer = new HeartbeatObserver();
        $observer->creating($heartbeat);

        $this->assertSame($expected, $heartbeat->hash);
    }
}
