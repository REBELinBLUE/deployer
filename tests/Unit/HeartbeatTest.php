<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Routing\UrlGenerator;
use Mockery as m;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Heartbeat
 */
class HeartbeatTest extends TestCase
{
    use TestsModel;

    /**
     * @dataProvider provideStatuses
     * @covers ::isHealthy
     */
    public function testIsHealthy($status, $expected)
    {
        $heartbeat = new Heartbeat();

        $this->assertFalse($heartbeat->isHealthy());

        $heartbeat->status = $status;

        $this->assertSame($expected, $heartbeat->isHealthy());
    }

    public function provideStatuses()
    {
        return $this->fixture('Heartbeat')['healthy'];
    }

    /**
     * @covers ::project
     */
    public function testProject()
    {
        $heartbeat = new Heartbeat();
        $actual    = $heartbeat->project();

        $this->assertInstanceOf(BelongsTo::class, $actual);
        $this->assertBelongsTo('project', Heartbeat::class);
    }

    /**
     * @covers ::generateHash
     */
    public function testGenerateHash()
    {
        $expected = 'a-random-url-token-hash';

        $this->mockTokenGenerator($expected);

        $heartbeat = new Heartbeat();
        $heartbeat->generateHash();

        $this->assertSame($expected, $heartbeat->hash);
    }

    /**
     * @covers ::getCallbackUrlAttribute
     */
    public function testGetCallbackUrlAttribute()
    {
        $hash     = 'a-url-safe-hash';
        $expected = 'http://localhost/heartbeat/' . $hash;

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('heartbeats', $hash, true)
             ->andReturn($expected);

        $this->app->instance('url', $mock);

        $this->mockTokenGenerator($hash);

        $heartbeat = new Heartbeat();
        $heartbeat->generateHash();

        $this->assertSame($expected, $heartbeat->callback_url);
        $this->assertSame($expected, $heartbeat->getCallbackUrlAttribute());
    }
}
