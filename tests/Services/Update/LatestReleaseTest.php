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

// Test isUpToDate
class LatestReleaseTest extends TestCase
{
    /**
     * @var \Illuminate\Contracts\Cache\Repository
     */
    private $cache;

    private $container = [];

    public function setUp()
    {
        parent::setUp();

        $this->cache = app('cache.store');
    }

    /**
     * @return Request
     */
    private function getRequest()
    {
        return $this->container[0]['request'];
    }

    private function mockHttpClient(Response $response)
    {
        $history = Middleware::history($this->container);
        $mock    = new MockHandler([$response]);
        $stack   = HandlerStack::create($mock);
        $stack->push($history);

        return new Client([
            'handler' => $stack,
        ]);
    }

    public function testRequestIsExpected()
    {
        $client = $this->mockHttpClient(new Response(200));

        $release = new LatestRelease($this->cache, $client);
        $release->latest();

        $request = $this->getRequest();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertTrue($request->hasHeader('Accept'));
        $this->assertEquals('application/vnd.github.v3+json', $request->getHeaderLine('Accept'));
    }

    public function testRequestHasExpectedAuthorizationHeader()
    {
        $client = $this->mockHttpClient(new Response(200));

        $expectedToken = '123456';

        $release = new LatestRelease($this->cache, $client, $expectedToken);
        $release->latest();

        $request = $this->getRequest();

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertEquals('token ' . $expectedToken, $request->getHeaderLine('Authorization'));
    }

    public function testRequestDoesNotHaveAuthorizationHeader()
    {
        $client = $this->mockHttpClient(new Response(200));

        $expectedToken = null;

        $release = new LatestRelease($this->cache, $client, $expectedToken);
        $release->latest();

        $request = $this->getRequest();

        $this->assertFalse($request->hasHeader('Authorization'));
    }

    public function testHandlesClientException()
    {
        $client = $this->mockHttpClient(new Response(500));

        $release = new LatestRelease($this->cache, $client);
        $actual  = $release->latest();

        $this->assertFalse($actual);
    }

    public function testHandlesInvalidResponseBody()
    {
        $client = $this->mockHttpClient(new Response(200, [], 'Unexpected response'));

        $release = new LatestRelease($this->cache, $client);
        $actual  = $release->latest();

        $this->assertFalse($actual);
    }

    public function testHandlesValidResponseBody()
    {
        $expected = 'an-expected-response';
        $response = json_encode(['tag_name' => $expected]);

        $client = $this->mockHttpClient(new Response(200, [], $response));

        $release = new LatestRelease($this->cache, $client);
        $actual  = $release->latest();

        $this->assertEquals($expected, $actual);
        $this->assertEquals(
            json_decode($response),
            $this->cache->get(LatestRelease::CACHE_KEY, null),
            'The expected response is not being cached'
        );
    }

    public function testIsUpToDateReturnsFalseOnOutdated()
    {
        // Is there not a nicer way to do this? Set the latest version to be very high
        $response = json_encode(['tag_name' => '1000.0.0']);

        $client  = $this->mockHttpClient(new Response(200, [], $response));
        $release = new LatestRelease($this->cache, $client);

        $this->assertFalse($release->isUpToDate());
    }

    public function testIsUpToDateReturnsTrueOnCurrent()
    {
        $response = json_encode(['tag_name' => APP_VERSION]);

        $client  = $this->mockHttpClient(new Response(200, [], $response));
        $release = new LatestRelease($this->cache, $client);

        $this->assertTrue($release->isUpToDate());
    }

    public function testIsUpToDateReturnsFalseOnPublishedVersionOlder()
    {
        $response = json_encode(['tag_name' => '0.0.1']);

        $client  = $this->mockHttpClient(new Response(200, [], $response));
        $release = new LatestRelease($this->cache, $client);

        $this->assertTrue($release->isUpToDate());
    }
}
