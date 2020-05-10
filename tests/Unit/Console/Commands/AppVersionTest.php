<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\AppVersion;
use REBELinBLUE\Deployer\Services\Update\LatestReleaseInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\AppVersion
 * @fixme: clean up duplicate CommandTester code
 */
class AppVersionTest extends TestCase
{
    private $release;

    private $console;

    protected function setUp(): void
    {
        parent::setUp();

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $this->release = m::mock(LatestReleaseInterface::class);
        $this->console = $console;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleWhenUpdated()
    {
        $this->release->shouldReceive('latest')->andReturn(APP_VERSION);
        $this->release->shouldReceive('isUpToDate')->andReturn(true);

        $command = new AppVersion($this->release);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'app:version',
        ]);

        $output = $tester->getDisplay();

        $this->assertStringContainsString('already running the latest version', $output);
        $this->assertStringNotContainsString('There is an update available', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::updateBanner
     */
    public function testHandleWhenOutdated()
    {
        $latest = '1000.0.01';

        $this->release->shouldReceive('latest')->andReturn($latest);
        $this->release->shouldReceive('isUpToDate')->andReturn(false);

        $command = new AppVersion($this->release);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'app:version',
        ]);

        $output = $tester->getDisplay();

        $this->assertStringNotContainsString('already running the latest version', $output);
        $this->assertStringContainsString('There is an update available!', $output);
        $this->assertStringContainsString($latest, $output);
        $this->assertStringContainsString(APP_VERSION, $output);
    }
}
