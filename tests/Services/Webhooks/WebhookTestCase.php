<?php

namespace REBELinBLUE\Deployer\Tests\Services\Webhooks;

use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;

abstract class WebhookTestCase extends TestCase
{
    public function getBranch()
    {
        return [
            'Branch' => ['master', 'heads/master'],
            'Tag'    => ['1.0.0', 'tags/1.0.0'],
        ];
    }

    /**
     * Asserts that webhook responses are as expected.
     *
     * @param string $actual
     * @param string $branch
     * @param string $source
     * @param string $reason
     * @param string $url
     * @param string $commit
     * @param string $name
     * @param string $email
     */
    protected function assertWebhookDataIsValid($actual, $branch, $source, $reason, $url, $commit, $name, $email)
    {
        $this->assertInternalType('array', $actual);

        $this->assertArrayHasKey('reason', $actual);
        $this->assertArrayHasKey('branch', $actual);
        $this->assertArrayHasKey('source', $actual);
        $this->assertArrayHasKey('build_url', $actual);
        $this->assertArrayHasKey('commit', $actual);
        $this->assertArrayHasKey('committer', $actual);
        $this->assertArrayHasKey('committer_email', $actual);

        $this->assertEquals($reason, $actual['reason']);
        $this->assertEquals($branch, $actual['branch']);
        $this->assertEquals($source, $actual['source']);
        $this->assertEquals($url, $actual['build_url']);
        $this->assertEquals($commit, $actual['commit']);
        $this->assertEquals($name, $actual['committer']);
        $this->assertEquals($email, $actual['committer_email']);
    }

    protected function mockRequestIsFrom($key, $isValid)
    {
        $request = $this->getMockBuilder(Request::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $header = $this->getMockBuilder(HeaderBag::class)
                       ->disableOriginalConstructor()
                       ->getMock();

        $header->expects($this->once())
               ->method('has')
               ->with($this->equalTo($key))
               ->willReturn($isValid);

        $request->headers = $header;

        return $request;
    }

    protected function mockEventRequest($key, $value)
    {
        $request = $this->getMockBuilder(Request::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('header')
                ->with($this->equalTo($key))
                ->willReturn($value);

        return $request;
    }
}
