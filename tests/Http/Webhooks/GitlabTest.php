<?php

namespace REBELinBLUE\Deployer\Tests\Http\Webhooks;

use Carbon\Carbon;
use REBELinBLUE\Deployer\Services\Webhooks\Gitlab;
use Symfony\Component\HttpFoundation\ParameterBag;

class GitlabTest extends WebhookTestCase
{
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
        $request = $this->mockEventRequestFromGitlab('Push Hook');

        $payload = $this->getMockBuilder(ParameterBag::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('json')
                ->willReturn($payload);

        $payload->expects($this->at(0))
                ->method('get')
                ->with($this->equalTo('commits'))
                ->willReturn($data);

        $payload->expects($this->at(1))
                ->method('get')
                ->with($this->equalTo('ref'))
                ->willReturn('refs/' . $ref);

        return $request;
    }

    public function testIsRequestOriginValid()
    {
        $request = $this->mockRequestIsFromGitlab(true);

        $gitlab = new Gitlab($request);
        $this->assertTrue($gitlab->isRequestOrigin());
    }

    public function testIsRequestOriginInvalid()
    {
        $request = $this->mockRequestIsFromGitlab(false);

        $gitlab = new Gitlab($request);
        $this->assertFalse($gitlab->isRequestOrigin());
    }

    /**
     * @param string $branch
     * @param string $ref
     * @dataProvider getBranch
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
     * @param string $event
     * @dataProvider getUnsupportedEvents
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
}
