<?php

namespace REBELinBLUE\Deployer\Tests\Services\Webhooks;

use Carbon\Carbon;
use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Services\Webhooks\Beanstalkapp;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;

class BeanstalkappTest extends WebhookTestCase
{
    private function mockRequestIsFromBeanstalk($isValid)
    {
        $request = $this->getMockBuilder(Request::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $header = $this->getMockBuilder(HeaderBag::class)
                       ->disableOriginalConstructor()
                       ->getMock();

        $header->expects($this->once())
               ->method('get')
               ->with($this->equalTo('User-Agent'))
               ->willReturn($isValid ? 'beanstalkapp.com' : 'something-else');

        $request->headers = $header;

        return $request;
    }

    private function mockEventRequestFromBeanstalk($event)
    {
        $request = $this->getMockBuilder(Request::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $payload = $this->getMockBuilder(ParameterBag::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('json')
                ->willReturn($payload);

        $payload->expects($this->once())
                ->method('has')
                ->with($this->equalTo('trigger'))
                ->willReturn(true);

        $payload->expects($this->once())
                ->method('get')
                ->with($this->equalTo('trigger'))
                ->willReturn($event);

        return $request;
    }

    private function mockRequestWithBeanstalkPayload(array $data)
    {
        $request = $this->getMockBuilder(Request::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $payload = $this->getMockBuilder(ParameterBag::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('json')
                ->willReturn($payload);

        $payload->expects($this->once())
                ->method('has')
                ->with($this->equalTo('trigger'))
                ->willReturn(true);

        $payload->expects($this->at(1))
                ->method('get')
                ->with($this->equalTo('trigger'))
                ->willReturn('push');

        $payload->expects($this->at(2))
                ->method('get')
                ->with($this->equalTo('payload'))
                ->willReturn($data);

        return $request;
    }

    public function testIsRequestOriginValid()
    {
        $request = $this->mockRequestIsFromBeanstalk(true);

        $beanstalkapp = new Beanstalkapp($request);
        $this->assertTrue($beanstalkapp->isRequestOrigin());
    }

    public function testIsRequestOriginInvalid()
    {
        $request = $this->mockRequestIsFromBeanstalk(false);

        $beanstalkapp = new Beanstalkapp($request);
        $this->assertFalse($beanstalkapp->isRequestOrigin());
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

        $request = $this->mockRequestWithBeanstalkPayload([
            'branch'  => $branch,
            'after'   => $commit,
            'commits' => [
                [
                    'committed_at'  => Carbon::now()->format('Y-m-d H:i:s'),
                    'changeset_url' => $url,
                    'message'       => $reason,
                    'author'        => [
                        'name'  => $name,
                        'email' => $email,
                    ],
                ],
            ],
        ]);

        $beanstalkapp = new Beanstalkapp($request);
        $actual       = $beanstalkapp->handlePush();

        $this->assertWebhookDataIsValid($actual, $branch, 'Beanstalkapp', $reason, $url, $commit, $name, $email);
    }

    /**
     * @param string $event
     * @dataProvider getUnsupportedEvents
     */
    public function testHandleUnsupportedEvent($event)
    {
        $request = $this->mockEventRequestFromBeanstalk($event);

        $beanstalkapp = new Beanstalkapp($request);
        $this->assertFalse($beanstalkapp->handlePush());
    }

    public function getUnsupportedEvents()
    {
        return array_chunk([
            'commit', 'comment', 'deploy', 'create_branch', 'delete_branch', 'create_tag', 'delete_tag',
            'request_code_review', 'cancel_code_review', 'reopen_code_review', 'approve_code_review',
        ], 1);
    }
}
