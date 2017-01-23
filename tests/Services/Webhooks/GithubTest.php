<?php

namespace REBELinBLUE\Deployer\Tests\Services\Webhooks;

use REBELinBLUE\Deployer\Services\Webhooks\Github;
use Symfony\Component\HttpFoundation\ParameterBag;

class GithubTest extends WebhookTestCase
{
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
        $request = $this->mockEventRequestFromGithub('push');

        $payload = $this->getMockBuilder(ParameterBag::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('json')
                ->willReturn($payload);

        $payload->expects($this->once())
                ->method('has')
                ->with($this->equalTo('after'))
                ->willReturn(true);

        $payload->expects($this->once())
                ->method('get')
                ->with($this->equalTo('after'))
                ->willReturn('0000000000000000000000000000000000000000');

        return $request;
    }

    private function mockRequestWithPayload(array $data, $ref)
    {
        $request = $this->mockEventRequestFromGithub('push');

        $payload = $this->getMockBuilder(ParameterBag::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('json')
                ->willReturn($payload);

        $payload->expects($this->once())
                ->method('has')
                ->with($this->equalTo('after'))
                ->willReturn(false);

        $payload->expects($this->at(1))
                ->method('get')
                ->with($this->equalTo('head_commit'))
                ->willReturn($data);

        $payload->expects($this->at(2))
                ->method('get')
                ->with($this->equalTo('ref'))
                ->willReturn('refs/' . $ref);

        return $request;
    }

    public function testIsRequestOriginValid()
    {
        $request = $this->mockRequestIsFromGithub(true);

        $github = new Github($request);
        $this->assertTrue($github->isRequestOrigin());
    }

    public function testIsRequestOriginInvalid()
    {
        $request = $this->mockRequestIsFromGithub(false);

        $github = new Github($request);
        $this->assertFalse($github->isRequestOrigin());
    }

    public function testHandleClosedPullRequest()
    {
        $request = $this->mockPullRequest();

        $github = new Github($request);
        $this->assertFalse($github->handlePush());
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
     * @param string $event
     * @dataProvider getUnsupportedEvents
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
}
