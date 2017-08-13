<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
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

        $mock = new MockHandler([
            new Response(200),
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $url = $this->mockCheckUrl($expected);
        $url->shouldReceive('setAttribute')->with('last_log', null);
        $url->shouldReceive('online')->once();
        $url->shouldNotReceive('offline');
        $url->shouldReceive('save');

        $links = new Collection();
        $links->push($url);

        $job = new RequestProjectCheckUrl($links);
        $job->handle($client);
    }

    /**
     * @covers ::handle
     * @covers ::__construct
     * @covers ::generateLog
     */
    public function testHandlesOffline()
    {
        $mockHandler = new MockHandler([
            new RequestException(
                'Error Communicating with Server',
                new Request('GET', '/', ['Host' => 'www.example.com']),
                null
            ),
        ]);

        $log = <<< EOF
Error Communicating with Server

--- Request ---
GET / HTTP/1.1
Host: www.example.com
EOF;

        $this->mockFailure($mockHandler, $log);
    }

    /**
     * @covers ::handle
     * @covers ::__construct
     * @covers ::generateLog
     */
    public function testHandlesOfflineWithResponse()
    {
        $mockHandler = new MockHandler([
            new RequestException(
                '`GET http://www.example.com/` resulted in a `404 Not Found` response:' . PHP_EOL . '<p>content</p>',
                new Request('GET', '/', ['Host' => 'www.example.com']),
                new Response(404, ['Content-Type' => 'text/html'], '<p>Not Found</p>')
            ),
        ]);

        $log = <<< EOF
`GET http://www.example.com/` resulted in a `404 Not Found` response

--- Request ---
GET / HTTP/1.1
Host: www.example.com

--- Response ---
HTTP/1.1 404 Not Found
Content-Type: text/html

<p>Not Found</p>
EOF;

        $this->mockFailure($mockHandler, $log);
    }

    /**
     * @covers ::handle
     * @covers ::__construct
     * @covers ::generateLog
     */
    public function testHandlesMultipleUrls()
    {
        $expected1 = 'http://www.google.com';
        $expected2 = 'http://www.example.com';

        $mock = new MockHandler([
            new Response(200),
            new RequestException(
                'Error Communicating with Server',
                new Request('GET', '/', ['Host' => 'www.example.com']),
                null
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $url1 = $this->mockCheckUrl($expected1);
        $url1->shouldReceive('setAttribute')->with('last_log', null);
        $url1->shouldReceive('online')->once();
        $url1->shouldNotReceive('offline');
        $url1->shouldReceive('save');

        $url2 = $this->mockCheckUrl($expected2);
        $url2->shouldReceive('setAttribute')->with('last_log', null);
        $url2->shouldReceive('setAttribute')->with('last_log', m::type('string'));
        $url2->shouldReceive('offline')->once();
        $url2->shouldNotReceive('online');
        $url2->shouldReceive('save');

        $links = new Collection([$url1, $url2]);

        $job = new RequestProjectCheckUrl($links);
        $job->handle($client);
    }

    /**
     * @covers ::__construct
     */
    public function testItHasUnlimitedTimeout()
    {
        $links = new Collection();

        $job = new RequestProjectCheckUrl($links);

        $this->assertSame(0, $job->timeout);
    }

    private function mockFailure($mockHandler, $log)
    {
        $expected = 'http://www.example.com/';

        $handler = HandlerStack::create($mockHandler);
        $client  = new Client(['handler' => $handler]);

        $url = $this->mockCheckUrl($expected);
        $url->shouldReceive('setAttribute')->once()->with('last_log', null);
        $url->shouldNotReceive('online');
        $url->shouldReceive('offline')->once();
        $url->shouldReceive('setAttribute')->once()->with('last_log', $log);
        $url->shouldReceive('save');

        $links = new Collection();
        $links->push($url);

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
