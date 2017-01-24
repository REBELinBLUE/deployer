<?php

namespace REBELinBLUE\Deployer\Tests\Services\Webhooks;

use Mockery as m;
use REBELinBLUE\Deployer\Services\Webhooks\Gogs;
use Symfony\Component\HttpFoundation\ParameterBag;

class GogsTest extends WebhookTestCase
{
    private function mockRequestIsFromGogs($isValid)
    {
        return $this->mockRequestIsFrom('X-Gogs-Event', $isValid);
    }

    private function mockEventRequestFromGogs($event)
    {
        return $this->mockEventRequest('X-Gogs-Event', $event);
    }

    private function mockRequestWithPayload(array $data, $ref)
    {
        $payload = m::mock(ParameterBag::class);
        $payload->shouldReceive('get')->with('commits')->andReturn($data);
        $payload->shouldReceive('get')->with('ref')->andReturn('refs/' . $ref);

        $request = $this->mockEventRequestFromGogs('push');
        $request->shouldReceive('json')->once()->andReturn($payload);

        return $request;
    }

    public function testIsRequestOriginValid()
    {
        $request = $this->mockRequestIsFromGogs(true);

        $gogs = new Gogs($request);
        $this->assertTrue($gogs->isRequestOrigin());
    }

    public function testIsRequestOriginInvalid()
    {
        $request = $this->mockRequestIsFromGogs(false);

        $gogs = new Gogs($request);
        $this->assertFalse($gogs->isRequestOrigin());
    }

    /**
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
            [
                'message'   => $reason,
                'url'       => $url,
                'id'        => $commit,
                'committer' => [
                    'name'  => $name,
                    'email' => $email,
                ],
            ],
        ], $ref);

        $gogs   = new Gogs($request);
        $actual = $gogs->handlePush();

        $this->assertWebhookDataIsValid($actual, $branch, 'Gogs', $reason, $url, $commit, $name, $email);
    }

    /**
     * @dataProvider getUnsupportedEvents
     */
    public function testHandleUnsupportedEvent($event)
    {
        $request = $this->mockEventRequestFromGogs($event);

        $gogs = new Gogs($request);
        $this->assertFalse($gogs->handlePush());
    }

    public function getUnsupportedEvents()
    {
        return array_chunk([
            'create', 'pull_request',
        ], 1);
    }
}
