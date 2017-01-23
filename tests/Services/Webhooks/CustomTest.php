<?php

namespace REBELinBLUE\Deployer\Tests\Services\Webhooks;

use Illuminate\Http\Request;
use Mockery;
use REBELinBLUE\Deployer\Services\Webhooks\Custom;
use Symfony\Component\HttpFoundation\HeaderBag;

class CustomTest extends WebhookTestCase
{
    private function mockRequestWithCustomPayload(array $data)
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('has')->once()->with('branch')->andReturn(true);
        $request->shouldReceive('get')->once()->with('branch')->andReturn($data['branch']);
        $request->shouldReceive('has')->once()->with('source')->andReturn(true);
        $request->shouldReceive('has')->once()->with('url')->andReturn(true);
        $request->shouldReceive('get')->once()->with('url')->andReturn($data['url']);
        $request->shouldReceive('has')->once()->with('commit')->andReturn(true);
        $request->shouldReceive('get')->once()->with('commit')->andReturn($data['commit']);
        $request->shouldReceive('get')->once()->with('reason')->andReturn($data['reason']);
        $request->shouldReceive('get')->once()->with('source')->andReturn($data['source']);

        return $request;
    }

    /**
     * @param string $branch
     * @dataProvider getBranch
     */
    public function testHandlePushEventValid($branch)
    {
        $reason = 'Commit Log';
        $url    = 'http://www.example.com/';
        $commit = 'ee5a7ef0b320eda038d0d376a6ce50c44475efae';
        $source = 'Custom';

        $request = $this->mockRequestWithCustomPayload([
            'branch'  => $branch,
            'source'  => $source,
            'url'     => $url,
            'commit'  => $commit,
            'reason'  => $reason,
        ]);

        $custom = new Custom($request);
        $actual = $custom->handlePush();

        $this->assertInternalType('array', $actual);

        $this->assertArrayHasKey('reason', $actual);
        $this->assertArrayHasKey('branch', $actual);
        $this->assertArrayHasKey('source', $actual);
        $this->assertArrayHasKey('build_url', $actual);
        $this->assertArrayHasKey('commit', $actual);

        $this->assertEquals($reason, $actual['reason']);
        $this->assertEquals($branch, $actual['branch']);
        $this->assertEquals($source, $actual['source']);
        $this->assertEquals($url, $actual['build_url']);
        $this->assertEquals($commit, $actual['commit']);
    }

    public function testIsRequestOriginValid()
    {
        $request = Mockery::mock(Request::class);

        $custom = new Custom($request);
        $this->assertTrue($custom->isRequestOrigin());
    }
}
