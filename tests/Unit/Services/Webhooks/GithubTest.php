<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Webhooks;

use Mockery as m;
use REBELinBLUE\Deployer\Services\Webhooks\Github;
use Symfony\Component\HttpFoundation\ParameterBag;

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
        $request = $this->mockRequestIsFromGithub(true);

        $github = new Github($request);
        $this->assertTrue($github->isRequestOrigin());
    }

    /**
     * @covers ::isRequestOrigin
     */
    public function testIsRequestOriginInvalid()
    {
        $request = $this->mockRequestIsFromGithub(false);

        $github = new Github($request);
        $this->assertFalse($github->isRequestOrigin());
    }

    /**
     * @covers ::handlePush
     */
    public function testHandleClosedPullRequest()
    {
        $request = $this->mockPullRequest();

        $github = new Github($request);
        $this->assertFalse($github->handlePush());
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
            'message'   => $reason,
            'url'       => $url,
            'id'        => $commit,
            'committer' => [
                'name'  => $name,
                'email' => $email,
            ],
        ], $ref);

        $github = new Github($request);
        $actual = $github->handlePush();

        $this->assertWebhookDataIsValid($actual, $branch, 'Github', $reason, $url, $commit, $name, $email);
    }

    /**
     * @dataProvider getUnsupportedEvents
     * @covers ::handlePush
     */
    public function testHandleUnsupportedEvent($event)
    {
        $request = $this->mockEventRequestFromGithub($event);

        $github = new Github($request);
        $this->assertFalse($github->handlePush());
    }

    public function getUnsupportedEvents()
    {
        return array_chunk([
            '*', 'commit_comment', 'create', 'delete', 'deployment', 'deployment_status', 'fork', 'gollum',
            'issue_comment', 'issues', 'label', 'member', 'membership', 'milestone', 'organization', 'page_build',
            'ping', 'public', 'pull_request_review_comment', 'pull_request_review', 'pull_request', 'repository',
            'release', 'status', 'team', 'team_add', 'watch',
        ], 1);
    }

    private function mockRequestIsFromGithub($isValid)
    {
        return $this->mockRequestIsFrom('X-GitHub-Event', $isValid);
    }

    private function mockEventRequestFromGithub($event)
    {
        return $this->mockEventRequest('X-GitHub-Event', $event);
    }

    private function mockPullRequest()
    {
        $payload = m::mock(ParameterBag::class);
        $payload->shouldReceive('has')->once()->with('after')->andReturn(true);
        $payload->shouldReceive('get')->once()->with('after')->andReturn('0000000000000000000000000000000000000000');

        $request = $this->mockEventRequestFromGithub('push');
        $request->shouldReceive('json')->once()->andReturn($payload);

        return $request;
    }

    private function mockRequestWithPayload(array $data, $ref)
    {
        $payload = m::mock(ParameterBag::class);
        $payload->shouldReceive('has')->once()->with('after')->andReturn(false);
        $payload->shouldReceive('get')->once()->with('head_commit')->andReturn($data);
        $payload->shouldReceive('get')->once()->with('ref')->andReturn('refs/' . $ref);

        $request = $this->mockEventRequestFromGithub('push');
        $request->shouldReceive('json')->once()->andReturn($payload);

        return $request;
    }
}
