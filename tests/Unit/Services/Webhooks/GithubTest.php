<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Webhooks;

use REBELinBLUE\Deployer\Services\Webhooks\Github;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Services\Webhooks\Github
 */
class GithubTest extends WebhookTestCase
{
    /**
     * @covers ::isRequestOrigin
     */
    public function testIsRequestOriginValid()
    {
        $request = $this->createRequestWithServiceHeader('X-GitHub-Event', true);

        $github = new Github($request);
        $this->assertTrue($github->isRequestOrigin());
    }

    /**
     * @covers ::isRequestOrigin
     */
    public function testIsRequestOriginInvalid()
    {
        $request = $this->createRequestWithServiceHeader('X-GitHub-Event', false);

        $github = new Github($request);
        $this->assertFalse($github->isRequestOrigin());
    }

    /**
     * @covers ::handlePush
     */
    public function testHandleClosedPullRequest()
    {
        $payload = [
            'after' => '0000000000000000000000000000000000000000',
        ];

        $request = $this->createRequestWithPayload('X-GitHub-Event', 'push', $payload);

        $github = new Github($request);
        $this->assertFalse($github->handlePush());
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
            'ref'         => 'refs/' . $ref,
            'head_commit' => [
                'message'   => $reason,
                'url'       => $url,
                'id'        => $commit,
                'committer' => [
                    'name'  => $name,
                    'email' => $email,
                ],
            ],
        ];

        $request = $this->createRequestWithPayload('X-GitHub-Event', 'push', $data);

        $github = new Github($request);
        $actual = $github->handlePush();

        $this->assertWebhookDataIsValid($actual, $branch, 'Github', $reason, $url, $commit, $name, $email);
    }

    /**
     * @dataProvider provideUnsupportedEvents
     * @covers ::handlePush
     */
    public function testHandleUnsupportedEvent($event)
    {
        $request = $this->createEventRequest('X-GitHub-Event', $event);

        $github = new Github($request);
        $this->assertFalse($github->handlePush());
    }

    public function provideUnsupportedEvents()
    {
        return array_chunk([
            '*', 'commit_comment', 'create', 'delete', 'deployment', 'deployment_status', 'fork', 'gollum',
            'issue_comment', 'issues', 'label', 'member', 'membership', 'milestone', 'organization', 'page_build',
            'ping', 'public', 'pull_request_review_comment', 'pull_request_review', 'pull_request', 'repository',
            'release', 'status', 'team', 'team_add', 'watch',
        ], 1);
    }
}
