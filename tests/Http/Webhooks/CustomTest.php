<?php

namespace REBELinBLUE\Deployer\Tests\Http\Webhooks;

use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Http\Webhooks\Custom;
use Symfony\Component\HttpFoundation\HeaderBag;

class CustomTest extends WebhookTestCase
{
    private function mockRequestWithCustomPayload(array $data)
    {
        $request = $this->getMockBuilder(Request::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        // This seems really fragile
        $request->expects($this->at(0))
                ->method('has')
                ->with($this->equalTo('branch'))
                ->willReturn(true);

        $request->expects($this->at(1))
                ->method('get')
                ->with($this->equalTo('branch'))
                ->willReturn($data['branch']);

        $request->expects($this->at(2))
                ->method('has')
                ->with($this->equalTo('source'))
                ->willReturn(true);

        $request->expects($this->at(3))
                ->method('has')
                ->with($this->equalTo('url'))
                ->willReturn(true);

        $request->expects($this->at(4))
                ->method('get')
                ->with($this->equalTo('url'))
                ->willReturn($data['url']);

        $request->expects($this->at(5))
                ->method('has')
                ->with($this->equalTo('commit'))
                ->willReturn(true);

        $request->expects($this->at(6))
                ->method('get')
                ->with($this->equalTo('commit'))
                ->willReturn($data['commit']);

        $request->expects($this->at(7))
                ->method('get')
                ->with($this->equalTo('reason'))
                ->willReturn($data['reason']);

        $request->expects($this->at(8))
                ->method('get')
                ->with($this->equalTo('source'))
                ->willReturn($data['source']);

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
        $request = $this->getMockBuilder(Request::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $custom = new Custom($request);
        $this->assertTrue($custom->isRequestOrigin());
    }
}
