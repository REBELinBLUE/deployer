<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Mockery as m;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Jobs\RequestProjectCheckUrl;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\RequestProjectCheckUrl
 */
class RequestProjectCheckUrlTest extends TestCase
{
    /**
     * @covers ::handle
     * @covers ::__construct
     */
    public function testHandlesOnline()
    {
        $expected = 'http://www.google.com';

        $client = m::mock(Client::class);
        $client->shouldReceive('get')->once()->with($expected);

        $url = $this->mockCheckUrl($expected);
        $url->shouldReceive('online')->once();
        $url->shouldNotReceive('offline');

        $links = new Collection();
        $links->push($url);

        $job = new RequestProjectCheckUrl($links);
        $job->handle($client);
    }

    /**
     * @covers ::handle
     * @covers ::__construct
     */
    public function testHandlesOffline()
    {
        $expected = 'http://www.google.com';

        $client = m::mock(Client::class);
        $client->shouldReceive('get')->once()->with($expected)->andThrow(Exception::class);

        $url = $this->mockCheckUrl($expected);
        $url->shouldReceive('offline')->once();
        $url->shouldNotReceive('online');

        $links = new Collection();
        $links->push($url);

        $job = new RequestProjectCheckUrl($links);
        $job->handle($client);
    }

    // FIXME: Should we test that the collection is looped through?

    private function mockCheckUrl($link)
    {
        $url = m::mock(CheckUrl::class);
        $url->shouldReceive('getAttribute')->once()->with('url')->andReturn($link);

        return $url;
    }
}
