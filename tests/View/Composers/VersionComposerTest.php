<?php

namespace REBELinBLUE\Deployer\Tests\View\Composers;

use Illuminate\Contracts\View\View;
use Mockery;
use REBELinBLUE\Deployer\Services\Github\LatestReleaseInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Composers\VersionComposer;
use Version\Version;

class VersionComposerTest extends TestCase
{
    public function testCompose()
    {
        $current = Version::parse(APP_VERSION);

        $release = Mockery::mock(LatestReleaseInterface::class);
        $release->shouldReceive('latest')->once()->andReturn(APP_VERSION);
        $release->shouldReceive('isUpToDate')->once()->andReturn(true);

        $view = Mockery::mock(View::class);
        $view->shouldReceive('with')->once()->with('is_outdated', false);
        $view->shouldReceive('with')->once()->with('current_version', (string) $current);
        $view->shouldReceive('with')->once()->with('latest_version', (string) $current);

        $composer = new VersionComposer($release);
        $composer->compose($view);
    }
}
