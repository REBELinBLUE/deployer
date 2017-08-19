<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Webhooks;

use Carbon\Carbon;
use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Services\Webhooks\Beanstalkapp;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Services\Webhooks\Beanstalkapp
 */
class BeanstalkappTest extends WebhookTestCase
{
    /**
     * @covers ::isRequestOrigin
     * @covers ::__construct
     */
    public function testIsRequestOriginValid()
    {
        $request = $this->createRequestFromBeanstalkWithPayload([], ['HTTP_USER_AGENT' => 'beanstalkapp.com']);

        $beanstalkapp = new Beanstalkapp($request);
        $this->assertTrue($beanstalkapp->isRequestOrigin());
    }

    /**
     * @covers ::isRequestOrigin
     */
    public function testIsRequestOriginInvalid()
    {
        $request = $this->createRequestFromBeanstalkWithPayload([], ['HTTP_USER_AGENT' => 'something-else']);

        $beanstalkapp = new Beanstalkapp($request);
        $this->assertFalse($beanstalkapp->isRequestOrigin());
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
            'trigger' => 'push',
            'payload' => [
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
            ],
        ];

        $request = $this->createRequestFromBeanstalkWithPayload($data);

        $beanstalkapp = new Beanstalkapp($request);
        $actual       = $beanstalkapp->handlePush();

        $this->assertWebhookDataIsValid($actual, $branch, 'Beanstalkapp', $reason, $url, $commit, $name, $email);
    }

    /**
     * @dataProvider provideUnsupportedEvents
     * @covers ::handlePush
     */
    public function testHandleUnsupportedEvent($event)
    {
        $request = $this->createRequestFromBeanstalkWithPayload(['trigger' => $event]);

        $beanstalkapp = new Beanstalkapp($request);
        $this->assertFalse($beanstalkapp->handlePush());
    }

    public function provideUnsupportedEvents()
    {
        return array_chunk([
            'commit', 'comment', 'deploy', 'create_branch', 'delete_branch', 'create_tag', 'delete_tag',
            'request_code_review', 'cancel_code_review', 'reopen_code_review', 'approve_code_review',
        ], 1);
    }

    protected function createRequestFromBeanstalkWithPayload(array $data, array $headers = [])
    {
        $headers = array_merge([
            'REQUEST_METHOD' => 'POST',
            'CONTENT_TYPE'   => 'application/json',
        ], $headers);

        return new Request([], [], [], [], [], $headers, json_encode($data));
    }
}
