<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Webhooks;

use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Tests\TestCase;

abstract class WebhookTestCase extends TestCase
{
    public function provideBranch(): array
    {
        return $this->fixture('Services/Webhooks/Webhook');
    }

    /**
     * Asserts that webhook responses are as expected.
     *
     * @param string $actual
     * @param string $branch
     * @param string $source
     * @param string $reason
     * @param string $url
     * @param string $commit
     * @param string $name
     * @param string $email
     */
    protected function assertWebhookDataIsValid(
        $actual,
        string $branch,
        string $source,
        string $reason,
        string $url,
        string $commit,
        string $name,
        string $email
    ): void {
        $this->assertIsArray($actual);

        $this->assertArrayHasKey('reason', $actual);
        $this->assertArrayHasKey('branch', $actual);
        $this->assertArrayHasKey('source', $actual);
        $this->assertArrayHasKey('build_url', $actual);
        $this->assertArrayHasKey('commit', $actual);
        $this->assertArrayHasKey('committer', $actual);
        $this->assertArrayHasKey('committer_email', $actual);

        $this->assertSame($reason, $actual['reason']);
        $this->assertSame($branch, $actual['branch']);
        $this->assertSame($source, $actual['source']);
        $this->assertSame($url, $actual['build_url']);
        $this->assertSame($commit, $actual['commit']);
        $this->assertSame($name, $actual['committer']);
        $this->assertSame($email, $actual['committer_email']);
    }

    // FIXME: Why is this needed? Refactor this mess
    protected function createRequestWithServiceHeader(string $key, bool $isValid): Request
    {
        $headers = [
            'REQUEST_METHOD' => 'POST',
            'CONTENT_TYPE'   => 'application/json',
        ];

        if ($isValid) {
            $key           = $this->headerToServerVar($key);
            $headers[$key] = true;
        }

        return new Request([], [], [], [], [], $headers);
    }

    protected function createEventRequest(string $key, string $value): Request
    {
        $header = $this->headerToServerVar($key);

        $headers = [
            $header          => $value,
            'REQUEST_METHOD' => 'POST',
            'CONTENT_TYPE'   => 'application/json',
        ];

        return new Request([], [], [], [], [], $headers);
    }

    protected function createRequestWithPayload(string $key, string $value, array $data): Request
    {
        $header = $this->headerToServerVar($key);

        $headers = [
            $header          => $value,
            'REQUEST_METHOD' => 'POST',
            'CONTENT_TYPE'   => 'application/json',
        ];

        return new Request([], [], [], [], [], $headers, json_encode($data));
    }

    private function headerToServerVar(string $key): string
    {
        return 'HTTP_' . str_replace('-', '_', strtoupper($key));
    }
}
