<?php

use REBELinBLUE\Deployer\View\Composers\VersionComposer;
use REBELinBLUE\Deployer\Contracts\Github\LatestReleaseInterface;
use Illuminate\Contracts\View\View;
use Version\Version;

class VersionComposerTest extends TestCase
{
    public function testCompose()
    {
        $current = Version::parse(APP_VERSION);

        $release = $this->getMockBuilder(LatestReleaseInterface::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $release->expects($this->once())
                ->method('latest')
                ->willReturn(APP_VERSION);

        $view = $this->getMockBuilder(View::class)
                     ->disableOriginalConstructor()
                     ->getMock();

        $view->expects($this->any())
             ->method('with')
             ->withConsecutive(
                 [$this->equalTo('is_outdated'), $this->equalTo(false)],
                 [$this->equalTo('current_version'), $this->equalTo($current)],
                 [$this->equalTo('latest_version'), $this->equalTo($current)]
             );

        $composer = new VersionComposer($release);
        $composer->compose($view);
    }
}
