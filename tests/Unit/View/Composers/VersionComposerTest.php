<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Composers;

use Illuminate\Contracts\View\View;
use Mockery as m;
use REBELinBLUE\Deployer\Services\Update\LatestReleaseInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Composers\VersionComposer;
use Version\Version;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Composers\VersionComposer
 */
class VersionComposerTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::compose
     */
    public function testCompose()
    {
        $current = Version::parse(APP_VERSION);

        $release = m::mock(LatestReleaseInterface::class);
        $release->shouldReceive('latest')->once()->andReturn(APP_VERSION);
        $release->shouldReceive('isUpToDate')->once()->andReturn(true);

        $view = m::mock(View::class);
        $view->shouldReceive('with')->once()->with('is_outdated', false);
        $view->shouldReceive('with')->once()->with('current_version', (string) $current);
        $view->shouldReceive('with')->once()->with('latest_version', (string) $current);

        $composer = new VersionComposer($release);
        $composer->compose($view);
    }
}
