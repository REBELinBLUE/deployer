<?php

namespace REBELinBLUE\Deployer\Tests\Services\Webhooks;

use Mockery;
use REBELinBLUE\Deployer\Services\Webhooks\Bitbucket;
use Symfony\Component\HttpFoundation\ParameterBag;

class BitbucketTest extends WebhookTestCase
{
    private function mockRequestIsFromBitbucket($isValid)
    {
        return $this->mockRequestIsFrom('X-Event-Key', $isValid);
    }

    private function mockEventRequestFromBitbucket($event)
    {
        return $this->mockEventRequest('X-Event-Key', $event);
    }

    private function mockRequestWithPayload(array $data)
    {
        $push = Mockery::mock(ParameterBag::class);
        $push->shouldReceive('has')->once()->with('changes')->andReturn(true);
        $push->shouldReceive('get')->once()->with('changes', [])->andReturn($data);
        $push->shouldReceive('get')->once()->with('changes')->andReturn($data);

        $payload = Mockery::mock(ParameterBag::class);
        $payload->shouldReceive('get')->once()->with('push')->andReturn($push);

        $request = $this->mockEventRequestFromBitbucket('repo:push');
        $request->shouldReceive('json')->once()->andReturn($payload);

        return $request;
    }

    public function testIsRequestOriginValid()
    {
        $request = $this->mockRequestIsFromBitbucket(true);

        $bitbucket = new Bitbucket($request);
        $this->assertTrue($bitbucket->isRequestOrigin());
    }

    public function testIsRequestOriginInvalid()
    {
        $request = $this->mockRequestIsFromBitbucket(false);

        $bitbucket = new Bitbucket($request);
        $this->assertFalse($bitbucket->isRequestOrigin());
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
        $name   = 'John Smith';
        $email  = 'john.smith@example.com';

        $payload = [
            [
                'new' => [
                    'name'   => $branch,
                    'target' => [
                        'message' => $reason,
                        'hash'    => $commit,
                        'author'  => ['raw' => $name . ' <' . $email . '>'],
                        'links'   => ['html' => ['href' => $url]],
                    ],
                ],
            ],
        ];

        $request = $this->mockRequestWithPayload($payload);

        $bitbucket = new Bitbucket($request);
        $actual    = $bitbucket->handlePush();

        $this->assertWebhookDataIsValid($actual, $branch, 'Bitbucket', $reason, $url, $commit, $name, $email);
    }

    /**
     * @param string $event
     * @dataProvider getUnsupportedEvents
     */
    public function testHandleUnsupportedEvent($event)
    {
        $request = $this->mockEventRequestFromBitbucket($event);

        $bitbucket = new Bitbucket($request);
        $this->assertFalse($bitbucket->handlePush());
    }

    public function getUnsupportedEvents()
    {
        return array_chunk([
            'repo:fork', 'repo:commit_comment_created', 'repo:commit_status_created', 'repo:commit_status_updated',
            'issue:created', 'issue:updated', 'issue:comment_created', 'pullrequest:created', 'pullrequest:updated',
            'pullrequest:approved', 'pullrequest:unapproved', 'pullrequest:fulfilled', 'pullrequest:rejected',
            'pullrequest:comment_created', 'pullrequest:comment_updated', 'pullrequest:comment_deleted',
        ], 1);
    }
}
