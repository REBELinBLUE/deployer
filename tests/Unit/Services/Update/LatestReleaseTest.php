<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Update;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use REBELinBLUE\Deployer\Services\Update\LatestRelease;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Services\Update\LatestRelease
 */
class LatestReleaseTest extends TestCase
{
    /**
     * @var \Illuminate\Contracts\Cache\Repository
     */
    private $cache;

    private $container = [];

    protected function setUp()
    {
        parent::setUp();

        $this->cache = $this->app->make('cache.store');
    }

    /**
     * @covers ::latest
     * @covers ::__construct
     */
    public function testRequestIsExpected()
    {
        $client = $this->mockHttpClient(new Response(200));

        $release = new LatestRelease($this->cache, $client);
        $release->latest();

        $request = $this->getRequest();

        $this->assertSame('GET', $request->getMethod());
        $this->assertTrue($request->hasHeader('Accept'));
        $this->assertSame('application/vnd.github.v3+json', $request->getHeaderLine('Accept'));
    }

    /**
     * @covers ::latest
     * @covers ::__construct
     */
    public function testRequestHasExpectedAuthorizationHeader()
    {
        $client = $this->mockHttpClient(new Response(200));

        $expectedToken = '123456';

        $release = new LatestRelease($this->cache, $client, $expectedToken);
        $release->latest();

        $request = $this->getRequest();

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertSame('token ' . $expectedToken, $request->getHeaderLine('Authorization'));
    }

    /**
     * @covers ::latest
     * @covers ::__construct
     */
    public function testRequestDoesNotHaveAuthorizationHeader()
    {
        $client = $this->mockHttpClient(new Response(200));

        $expectedToken = null;

        $release = new LatestRelease($this->cache, $client, $expectedToken);
        $release->latest();

        $request = $this->getRequest();

        $this->assertFalse($request->hasHeader('Authorization'));
    }

    /**
     * @covers ::latest
     * @covers ::__construct
     */
    public function testHandlesClientException()
    {
        $client = $this->mockHttpClient(new Response(500));

        $release = new LatestRelease($this->cache, $client);
        $actual  = $release->latest();

        $this->assertFalse($actual);
    }

    /**
     * @covers ::latest
     * @covers ::__construct
     */
    public function testHandlesInvalidResponseBody()
    {
        $client = $this->mockHttpClient(new Response(200, [], 'Unexpected response'));

        $release = new LatestRelease($this->cache, $client);
        $actual  = $release->latest();

        $this->assertFalse($actual);
    }

    /**
     * @covers ::latest
     * @covers ::__construct
     */
    public function testHandlesValidResponseBody()
    {
        $expected = 'an-expected-response';
        $response = json_encode(['tag_name' => $expected]);

        $client = $this->mockHttpClient(new Response(200, [], $response));

        $release = new LatestRelease($this->cache, $client);
        $actual  = $release->latest();

        $this->assertSame($expected, $actual);
        $this->assertSame(
            $response,
            json_encode($this->cache->get(LatestRelease::CACHE_KEY, null)),
            'The expected response is not being cached'
        );
    }

    /**
     * @covers ::isUpToDate
     * @covers ::__construct
     */
    public function testIsUpToDateReturnsFalseOnOutdated()
    {
        // Is there not a nicer way to do this? Set the latest version to be very high
        $response = json_encode(['tag_name' => '1000.0.0']);

        $client  = $this->mockHttpClient(new Response(200, [], $response));
        $release = new LatestRelease($this->cache, $client);

        $this->assertFalse($release->isUpToDate());
    }

    /**
     * @covers ::isUpToDate
     * @covers ::__construct
     */
    public function testIsUpToDateReturnsTrueOnCurrent()
    {
        $response = json_encode(['tag_name' => APP_VERSION]);

        $client  = $this->mockHttpClient(new Response(200, [], $response));
        $release = new LatestRelease($this->cache, $client);

        $this->assertTrue($release->isUpToDate());
    }

    /**
     * @covers ::isUpToDate
     * @covers ::__construct
     */
    public function testIsUpToDateReturnsFalseOnPublishedVersionOlder()
    {
        $response = json_encode(['tag_name' => '0.0.1']);

        $client  = $this->mockHttpClient(new Response(200, [], $response));
        $release = new LatestRelease($this->cache, $client);

        $this->assertTrue($release->isUpToDate());
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
}
