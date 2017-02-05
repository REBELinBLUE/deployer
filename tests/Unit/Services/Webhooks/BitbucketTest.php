<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Webhooks;

use Mockery as m;
use REBELinBLUE\Deployer\Services\Webhooks\Bitbucket;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Services\Webhooks\Bitbucket
 */
class BitbucketTest extends WebhookTestCase
{
    /**
     * @covers ::isRequestOrigin
     */
    public function testIsRequestOriginValid()
    {
        $request = $this->mockRequestIsFromBitbucket(true);

        $bitbucket = new Bitbucket($request);
        $this->assertTrue($bitbucket->isRequestOrigin());
    }

    /**
     * @covers ::isRequestOrigin
     */
    public function testIsRequestOriginInvalid()
    {
        $request = $this->mockRequestIsFromBitbucket(false);

        $bitbucket = new Bitbucket($request);
        $this->assertFalse($bitbucket->isRequestOrigin());
    }

    /**
     * @dataProvider provideBranch
     * @covers ::handlePush
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
     * @covers ::handlePush
     */
    public function testHandlePushWithoutChanges()
    {
        $push = m::mock(ParameterBag::class);
        $push->shouldReceive('has')->once()->with('changes')->andReturn(false);

        $payload = m::mock(ParameterBag::class);
        $payload->shouldReceive('get')->once()->with('push')->andReturn($push);

        $request = $this->mockEventRequestFromBitbucket('repo:push');
        $request->shouldReceive('json')->once()->andReturn($payload);

        $bitbucket = new Bitbucket($request);
        $this->assertFalse($bitbucket->handlePush());
    }

    /**
     * @covers ::handlePush
     */
    public function testHandlePushWithEmptyChanges()
    {
        $request = $this->mockRequestWithPayload([]);

        $bitbucket = new Bitbucket($request);
        $this->assertFalse($bitbucket->handlePush());
    }

    /**
     * @dataProvider provideUnsupportedEvents
     * @covers ::handlePush
     */
    public function testHandleUnsupportedEvent($event)
    {
        $request = $this->mockEventRequestFromBitbucket($event);

        $bitbucket = new Bitbucket($request);
        $this->assertFalse($bitbucket->handlePush());
    }

    public function provideUnsupportedEvents()
    {
        return array_chunk([
            'repo:fork', 'repo:commit_comment_created', 'repo:commit_status_created', 'repo:commit_status_updated',
            'issue:created', 'issue:updated', 'issue:comment_created', 'pullrequest:created', 'pullrequest:updated',
            'pullrequest:approved', 'pullrequest:unapproved', 'pullrequest:fulfilled', 'pullrequest:rejected',
            'pullrequest:comment_created', 'pullrequest:comment_updated', 'pullrequest:comment_deleted',
        ], 1);
    }

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
        $push = m::mock(ParameterBag::class);
        $push->shouldReceive('has')->once()->with('changes')->andReturn(true);
        $push->shouldReceive('get')->once()->with('changes', [])->andReturn($data);

        $payload = m::mock(ParameterBag::class);
        $payload->shouldReceive('get')->once()->with('push')->andReturn($push);

        $request = $this->mockEventRequestFromBitbucket('repo:push');
        $request->shouldReceive('json')->once()->andReturn($payload);

        return $request;
    }
}
