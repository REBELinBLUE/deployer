<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs\DeployProject;

use REBELinBLUE\Deployer\Jobs\DeployProject\LogFormatter;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\DeployProject\LogFormatter
 */
class LogFormatterTest extends TestCase
{
    /**
     * @covers ::error
     */
    public function testError()
    {
        $formatter = new LogFormatter();
        $actual    = $formatter->error('message');

        $this->assertSame('<error>message</error>', $actual);
    }

    /**
     * @covers ::info
     */
    public function testInfo()
    {
        $formatter = new LogFormatter();
        $actual    = $formatter->info('message');

        $this->assertSame('<info>message</info>', $actual);
    }
}
