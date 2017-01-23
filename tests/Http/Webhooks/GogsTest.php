<?php

namespace REBELinBLUE\Deployer\Tests\Http\Webhooks;

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
        $request = $this->mockEventRequestFromGogs('push');

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
     * @param string $event
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
