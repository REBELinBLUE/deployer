<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\AppVersion;
use REBELinBLUE\Deployer\Services\Update\LatestReleaseInterface;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\AppVersion
 */
class AppVersionTest extends CommandTestCase
{
    /**
     * @var LatestReleaseInterface
     */
    private $release;

    public function setUp()
    {
        parent::setUp();

        $this->release = m::mock(LatestReleaseInterface::class);
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

        $output = $this->runCommand($command);

        $this->assertContains('already running the latest version', $output);
        $this->assertNotContains('There is an update available', $output);
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

        $output = $this->runCommand($command);

        $this->assertNotContains('already running the latest version', $output);
        $this->assertContains('There is an update available!', $output);
        $this->assertContains($latest, $output);
        $this->assertContains(APP_VERSION, $output);
    }
}
