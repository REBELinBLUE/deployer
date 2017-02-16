<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Carbon\Carbon;
use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\AbortDeployment;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\AbortDeployment
 */
class AbortDeploymentTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle()
    {
        $expected_id        = 10;
        $key                = AbortDeployment::CACHE_KEY_PREFIX . $expected_id;
        $timestamp          = 1452870024;

        Carbon::setTestNow(Carbon::create(2016, 1, 15, 15, 00, 24, 'UTC'));

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->once()->with('id')->andReturn($expected_id);

        $cache = $this->app->make('cache.store');

        $job = new AbortDeployment($deployment);
        $job->handle($cache);

        $this->assertSame($timestamp, $cache->get($key));
    }
}
