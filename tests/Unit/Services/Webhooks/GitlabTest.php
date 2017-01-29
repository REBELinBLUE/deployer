<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Webhooks;

use Carbon\Carbon;
use Mockery as m;
use REBELinBLUE\Deployer\Services\Webhooks\Gitlab;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Services\Webhooks\Gitlab
 */
class GitlabTest extends WebhookTestCase
{
    /**
     * @covers ::isRequestOrigin
     */
    public function testIsRequestOriginValid()
    {
        $request = $this->mockRequestIsFromGitlab(true);

        $gitlab = new Gitlab($request);
        $this->assertTrue($gitlab->isRequestOrigin());
    }

    /**
     * @covers ::isRequestOrigin
     */
    public function testIsRequestOriginInvalid()
    {
        $request = $this->mockRequestIsFromGitlab(false);

        $gitlab = new Gitlab($request);
        $this->assertFalse($gitlab->isRequestOrigin());
    }

    /**
     * @dataProvider getBranch
     * @covers ::handlePush
     */
    public function testHandlePushEventValid($branch, $ref)
    {
        $reason = 'Commit Log';
        $url    = 'http://www.example.com/';
        $commit = 'ee5a7ef0b320eda038d0d376a6ce50c44475efae';
        $name   = 'John Smith';
        $email  = 'john.smith@example.com';

        $request = $this->mockRequestWithPayload([
            'commits' => [
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s'),
                'message'   => $reason,
                'url'       => $url,
                'id'        => $commit,
                'author'    => [
                    'name'  => $name,
                    'email' => $email,
                ],
            ],
        ], $ref);

        $gitlab = new Gitlab($request);
        $actual = $gitlab->handlePush();

        $this->assertWebhookDataIsValid($actual, $branch, 'Gitlab', $reason, $url, $commit, $name, $email);
    }

    /**
     * @dataProvider getUnsupportedEvents
     * @covers ::handlePush
     */
    public function testHandleUnsupportedEvent($event)
    {
        $request = $this->mockEventRequestFromGitlab($event);

        $gitlab = new Gitlab($request);
        $this->assertFalse($gitlab->handlePush());
    }

    public function getUnsupportedEvents()
    {
        return array_chunk([
            'System Hook', 'Issue Hook', 'Note Hook', 'Merge Request Hook',
            'Wiki Page Hook', 'Pipeline Hook', 'Build Hook',
        ], 1);
    }

    private function mockRequestIsFromGitlab($isValid)
    {
        return $this->mockRequestIsFrom('X-Gitlab-Event', $isValid);
    }

    private function mockEventRequestFromGitlab($event)
    {
        return $this->mockEventRequest('X-Gitlab-Event', $event);
    }

    private function mockRequestWithPayload(array $data, $ref)
    {
        $payload = m::mock(ParameterBag::class);
        $payload->shouldReceive('get')->once()->with('commits')->andReturn($data);
        $payload->shouldReceive('get')->once()->with('ref')->andReturn('refs/' . $ref);

        $request = $this->mockEventRequestFromGitlab('Push Hook');
        $request->shouldReceive('json')->once()->andReturn($payload);

        return $request;
    }
}
