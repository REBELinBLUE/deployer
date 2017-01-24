<?php

namespace REBELinBLUE\Deployer\Tests\Services\Update;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery as m;
use REBELinBLUE\Deployer\Services\Update\LatestRelease;
use REBELinBLUE\Deployer\Tests\TestCase;

// Clean this up, test isUpToDate and check cache works
class LatestReleaseTest extends TestCase
{
    /**
     * @var \Illuminate\Contracts\Cache\Repository
     */
    private $cache;

    public function setUp()
    {
        parent::setUp();

        $this->cache = app('cache.store');
    }

    public function testRequestIsExpected()
    {
        $container = [];
        $history   = Middleware::history($container);

        $mock = new MockHandler([
            new Response(200),
        ]);

        $stack = HandlerStack::create($mock);
        $stack->push($history);

        $client = new Client(['handler' => $stack]);

        $release = new LatestRelease($this->cache, $client);
        $release->latest();

        /** @var Request $request */
        $request = $container[0]['request'];

        $this->assertEquals('GET', $request->getMethod());
        $this->assertTrue($request->hasHeader('Accept'));
        $this->assertEquals('application/vnd.github.v3+json', $request->getHeaderLine('Accept'));
    }

    public function testRequestHasExpectedAuthorizationHeader()
    {
        $expectedToken = '123456';

        $container = [];
        $history   = Middleware::history($container);

        $mock = new MockHandler([
            new Response(200),
        ]);

        $stack = HandlerStack::create($mock);
        $stack->push($history);

        $client = new Client(['handler' => $stack]);

        $release = new LatestRelease($this->cache, $client, $expectedToken);
        $release->latest();

        /** @var Request $request */
        $request = $container[0]['request'];

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertEquals('token ' . $expectedToken, $request->getHeaderLine('Authorization'));
    }

    public function testRequestDoesNotHaveAuthorizationHeader()
    {
        $expectedToken = null;

        $container = [];
        $history   = Middleware::history($container);

        $mock = new MockHandler([
            new Response(200),
        ]);

        $stack = HandlerStack::create($mock);
        $stack->push($history);

        $client = new Client(['handler' => $stack]);

        $release = new LatestRelease($this->cache, $client, $expectedToken);
        $release->latest();

        /** @var Request $request */
        $request = $container[0]['request'];

        $this->assertFalse($request->hasHeader('Authorization'));
    }

    public function testHandlesClientException()
    {
        $mock = new MockHandler([
            new Response(500),
        ]);

        $stack  = HandlerStack::create($mock);
        $client = new Client(['handler' => $stack]);

        $release = new LatestRelease($this->cache, $client);
        $actual  = $release->latest();

        $this->assertFalse($actual);
    }

    public function testHandlesInvalidResponseBody()
    {
        $mock = new MockHandler([
            new Response(200, [], 'Unexpected response'),
        ]);

        $stack  = HandlerStack::create($mock);
        $client = new Client(['handler' => $stack]);

        $release = new LatestRelease($this->cache, $client);
        $actual  = $release->latest();

        $this->assertFalse($actual);
    }

    public function testHandlesValidResponseBody()
    {
        $expected = 'an-expected-response';
        $response = json_encode(['tag_name' => $expected]);

        $mock = new MockHandler([
            new Response(200, [], $response),
        ]);

        $stack  = HandlerStack::create($mock);
        $client = new Client(['handler' => $stack]);

        $release = new LatestRelease($this->cache, $client);
        $actual  = $release->latest();

        $this->assertEquals($expected, $actual);
        $this->assertEquals(
            json_decode($response),
            $this->cache->get(LatestRelease::CACHE_KEY, null),
            'The expected response is not being cached'
        );
    }
}
