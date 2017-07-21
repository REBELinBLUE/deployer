<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Webhooks;

use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Services\Webhooks\Custom;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Services\Webhooks\Custom
 */
class CustomTest extends WebhookTestCase
{
    /**
     * @dataProvider provideBranch
     * @covers ::handlePush
     */
    public function testHandlePushEventValid($branch)
    {
        $reason = 'Commit Log';
        $url    = 'http://www.example.com/';
        $commit = 'ee5a7ef0b320eda038d0d376a6ce50c44475efae';
        $source = 'Custom';

        $request = new Request([
            'branch'  => $branch,
            'source'  => $source,
            'url'     => $url,
            'commit'  => $commit,
            'reason'  => $reason,
        ]);

        $custom = new Custom($request);
        $actual = $custom->handlePush();

        $this->assertInternalType('array', $actual);

        $this->assertArrayHasKey('reason', $actual);
        $this->assertArrayHasKey('branch', $actual);
        $this->assertArrayHasKey('source', $actual);
        $this->assertArrayHasKey('build_url', $actual);
        $this->assertArrayHasKey('commit', $actual);

        $this->assertSame($reason, $actual['reason']);
        $this->assertSame($branch, $actual['branch']);
        $this->assertSame($source, $actual['source']);
        $this->assertSame($url, $actual['build_url']);
        $this->assertSame($commit, $actual['commit']);
    }

    /**
     * @covers ::handlePush
     */
    public function testInvalidCommitIsCleared()
    {
        $request = new Request([
            'branch'  => 'master',
            'source'  => 'custom',
            'commit'  => 'short',
            'url'     => '',
            'reason'  => '',
        ]);

        $custom = new Custom($request);
        $actual = $custom->handlePush();

        $this->assertEmpty($actual['commit']);
    }

    /**
     * @covers ::handlePush
     */
    public function testInvalidUrlIsCleared()
    {
        $request = new Request([
            'branch'  => 'master',
            'source'  => 'ee5a7ef',
            'commit'  => 'short',
            'url'     => 'invalid-url',
            'reason'  => '',
        ]);

        $custom = new Custom($request);
        $actual = $custom->handlePush();

        $this->assertEmpty($actual['commit']);
    }

    /**
     * @covers ::isRequestOrigin
     */
    public function testIsRequestOriginValid()
    {
        $request = new Request();

        $custom = new Custom($request);
        $this->assertTrue($custom->isRequestOrigin());
    }
}
