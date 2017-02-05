<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Webhooks;

use Illuminate\Http\Request;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;

abstract class WebhookTestCase extends TestCase
{
    public function provideBranch()
    {
        return $this->fixture('Services/Webhooks/Webhook');
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

        $this->assertSame($reason, $actual['reason']);
        $this->assertSame($branch, $actual['branch']);
        $this->assertSame($source, $actual['source']);
        $this->assertSame($url, $actual['build_url']);
        $this->assertSame($commit, $actual['commit']);
        $this->assertSame($name, $actual['committer']);
        $this->assertSame($email, $actual['committer_email']);
    }

    protected function mockRequestIsFrom($key, $isValid)
    {
        $header = m::mock(HeaderBag::class);
        $header->shouldReceive('has')->once()->with($key)->andReturn($isValid);

        $request          = m::mock(Request::class);
        $request->headers = $header;

        return $request;
    }

    protected function mockEventRequest($key, $value)
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('header')->once()->with($key)->andReturn($value);

        return $request;
    }
}
