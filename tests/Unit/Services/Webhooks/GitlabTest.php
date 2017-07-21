<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Webhooks;

use Carbon\Carbon;
use Mockery as m;
use REBELinBLUE\Deployer\Services\Webhooks\Gitlab;

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
        $request = $this->createRequestWithServiceHeader('X-Gitlab-Event', true);

        $gitlab = new Gitlab($request);
        $this->assertTrue($gitlab->isRequestOrigin());
    }

    /**
     * @covers ::isRequestOrigin
     */
    public function testIsRequestOriginInvalid()
    {
        $request = $this->createRequestWithServiceHeader('X-Gitlab-Event', false);

        $gitlab = new Gitlab($request);
        $this->assertFalse($gitlab->isRequestOrigin());
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
            ],
        ];

        $request = $this->createRequestWithPayload('X-Gitlab-Event', 'Push Hook', $data);

        $gitlab = new Gitlab($request);
        $actual = $gitlab->handlePush();

        $this->assertWebhookDataIsValid($actual, $branch, 'Gitlab', $reason, $url, $commit, $name, $email);
    }

    /**
     * @dataProvider provideUnsupportedEvents
     * @covers ::handlePush
     */
    public function testHandleUnsupportedEvent($event)
    {
        $request = $this->createEventRequest('X-Gitlab-Event', $event);

        $gitlab = new Gitlab($request);
        $this->assertFalse($gitlab->handlePush());
    }

    public function provideUnsupportedEvents()
    {
        return array_chunk([
            'System Hook', 'Issue Hook', 'Note Hook', 'Merge Request Hook',
            'Wiki Page Hook', 'Pipeline Hook', 'Build Hook',
        ], 1);
    }
}
