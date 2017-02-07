<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Mockery as m;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Jobs\RequestProjectCheckUrl;
use REBELinBLUE\Deployer\Tests\TestCase;

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

    /**
     * @covers ::handle
     * @covers ::__construct
     */
    public function testHandlesMultiple()
    {
        $expected1 = 'http://www.google.com';
        $expected2 = 'http://www.example.com';

        $client = m::mock(Client::class);
        $client->shouldReceive('get')->once()->with($expected1);
        $client->shouldReceive('get')->once()->with($expected2)->andThrow(Exception::class);

        $url1 = $this->mockCheckUrl($expected1);
        $url1->shouldReceive('online')->once();
        $url1->shouldNotReceive('offline');

        $url2 = $this->mockCheckUrl($expected2);
        $url2->shouldReceive('offline')->once();
        $url2->shouldNotReceive('online');

        $links = new Collection();
        $links->push($url1);
        $links->push($url2);

        $job = new RequestProjectCheckUrl($links);
        $job->handle($client);
    }

    private function mockCheckUrl($link)
    {
        $url = m::mock(CheckUrl::class);
        $url->shouldReceive('getAttribute')->once()->with('url')->andReturn($link);

        return $url;
    }
}
