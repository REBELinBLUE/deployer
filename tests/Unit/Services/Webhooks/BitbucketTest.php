<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Webhooks;

use REBELinBLUE\Deployer\Services\Webhooks\Bitbucket;

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
        $request = $this->createRequestWithServiceHeader('X-Event-Key', true);

        $bitbucket = new Bitbucket($request);
        $this->assertTrue($bitbucket->isRequestOrigin());
    }

    /**
     * @covers ::isRequestOrigin
     */
    public function testIsRequestOriginInvalid()
    {
        $request = $this->createRequestWithServiceHeader('X-Event-Key', false);

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

        $data = [
            'push' => [
                'changes' => [
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
                ],
            ],
        ];

        $request = $this->createRequestWithPayload('X-Event-Key', 'repo:push', $data);

        $bitbucket = new Bitbucket($request);
        $actual    = $bitbucket->handlePush();

        $this->assertWebhookDataIsValid($actual, $branch, 'Bitbucket', $reason, $url, $commit, $name, $email);
    }

    /**
     * @covers ::handlePush
     */
    public function testHandlePushWithoutChanges()
    {
        $data = [
            'push' => [],
        ];

        $request = $this->createRequestWithPayload('X-Event-Key', 'repo:push', $data);

        $bitbucket = new Bitbucket($request);
        $this->assertFalse($bitbucket->handlePush());
    }

    /**
     * @covers ::handlePush
     */
    public function testHandlePushWithEmptyChanges()
    {
        $data = [
            'push' => [
                'changes' => [],
            ],
        ];

        $request = $this->createRequestWithPayload('X-Event-Key', 'repo:push', $data);

        $bitbucket = new Bitbucket($request);
        $this->assertFalse($bitbucket->handlePush());
    }

    /**
     * @dataProvider provideUnsupportedEvents
     * @covers ::handlePush
     */
    public function testHandleUnsupportedEvent($event)
    {
        $request = $this->createEventRequest('X-Event-Key', $event);

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
}
