<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Webhooks;

use REBELinBLUE\Deployer\Services\Webhooks\Gogs;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Services\Webhooks\Gogs
 */
class GogsTest extends WebhookTestCase
{
    /**
     * @covers ::isRequestOrigin
     */
    public function testIsRequestOriginValid()
    {
        $request = $this->createRequestWithServiceHeader('X-Gogs-Event', true);

        $gogs = new Gogs($request);
        $this->assertTrue($gogs->isRequestOrigin());
    }

    /**
     * @covers ::isRequestOrigin
     */
    public function testIsRequestOriginInvalid()
    {
        $request = $this->createRequestWithServiceHeader('X-Gogs-Event', false);

        $gogs = new Gogs($request);
        $this->assertFalse($gogs->isRequestOrigin());
    }

    /**
     * @dataProvider provideBranch
     * @covers ::handlePush
     */
    public function testHandlePushEventValid($branch, $ref)
    {
        $reason = 'Commit Log';
        $url    = 'http://www.example.com/';
        $commit = 'ee5a7ef0b320eda038d0d376a6ce50c44475efae';
        $name   = 'John Smith';
        $email  = 'john.smith@example.com';

        $data = [
            'ref'     => 'refs/' . $ref,
            'commits' => [
                [
                    'message'   => $reason,
                    'url'       => $url,
                    'id'        => $commit,
                    'committer' => [
                        'name'  => $name,
                        'email' => $email,
                    ],
                ],
            ],
        ];

        $request = $this->createRequestWithPayload('X-Gogs-Event', 'push', $data);

        $gogs   = new Gogs($request);
        $actual = $gogs->handlePush();

        $this->assertWebhookDataIsValid($actual, $branch, 'Gogs', $reason, $url, $commit, $name, $email);
    }

    /**
     * @covers ::handlePush
     */
    public function testHandleEmptyPush()
    {
        $request = $this->createRequestWithPayload('X-Gogs-Event', 'push', ['commits' => []]);

        $gogs = new Gogs($request);
        $this->assertFalse($gogs->handlePush());
    }

    /**
     * @dataProvider provideUnsupportedEvents
     * @covers ::handlePush
     */
    public function testHandleUnsupportedEvent($event)
    {
        $request = $this->createEventRequest('X-Gogs-Event', $event);

        $gogs = new Gogs($request);
        $this->assertFalse($gogs->handlePush());
    }

    public function provideUnsupportedEvents()
    {
        return array_chunk([
            'create', 'pull_request',
        ], 1);
    }
}
