<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Presenters;

use Illuminate\Contracts\Translation\Translator;
use Mockery as m;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Presenters\ProjectPresenter;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Presenters\ProjectPresenter
 */
class ProjectPresenterTest extends TestCase
{
    private $translator;

    public function setUp()
    {
        parent::setUp();

        $this->translator = m::mock(Translator::class);
    }

    /**
     * @dataProvider provideCCTrayStatus
     * @covers ::presentCcTrayStatus
     */
    public function testPresentCcTrayStatusIsCorrect($status, $expected)
    {
        $project = $this->mockProjectWithStatus($status);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentCcTrayStatus();

        $this->assertSame($expected, $actual);
    }

    public function provideCCTrayStatus()
    {
        return $this->fixture('View/Presenters/ProjectPresenter')['cc_tray_status'];
    }

    /**
     * @dataProvider provideReadableStatus
     * @covers ::presentReadableStatus
     */
    public function testPresentReadableStatusIsCorrect($status, $expected)
    {
        $project = $this->mockProjectWithStatus($status);

        $this->translator->shouldReceive('trans')->once()->with($expected)->andReturn($expected);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentReadableStatus();

        $this->assertSame($expected, $actual);
    }

    public function provideReadableStatus()
    {
        return $this->fixture('View/Presenters/ProjectPresenter')['readable_status'];
    }

    /**
     * @dataProvider provideIcons
     * @covers ::presentIcon
     */
    public function testPresentIconIsCorrect($status, $expected)
    {
        $project = $this->mockProjectWithStatus($status);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentIcon();

        $this->assertSame($expected, $actual);
    }

    public function provideIcons()
    {
        return $this->fixture('View/Presenters/ProjectPresenter')['icons'];
    }

    /**
     * @dataProvider provideCssClasses
     * @covers ::presentCssClass
     */
    public function testPresentCssClassIsCorrect($status, $expected)
    {
        $project = $this->mockProjectWithStatus($status);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentCssClass();

        $this->assertSame($expected, $actual);
    }

    public function provideCssClasses()
    {
        return $this->fixture('View/Presenters/ProjectPresenter')['css_classes'];
    }

    /**
     * @covers ::presentAppStatus
     * @covers ::getStatusLabel
     */
    public function testPresentAppStatusReturnsMessageOnEmpty()
    {
        $expected = 'app.not_applicable';

        $project = $this->mockProjectWithHealthStatus(0, 0);

        $this->translator->shouldReceive('trans')->once()->with($expected)->andReturn($expected);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentAppStatus();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideAppStatuses
     * @covers ::presentAppStatus
     * @covers ::getStatusLabel
     */
    public function testPresentAppStatusReturnsExpectedMessageWhenNotEmpty($length, $missed, $expected)
    {
        $project = $this->mockProjectWithHealthStatus($length, $missed);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentAppStatus();

        $this->assertSame($expected, $actual);
    }

    public function provideAppStatuses()
    {
        return $this->fixture('View/Presenters/ProjectPresenter')['status_label'];
    }

    /**
     * @dataProvider provideAppStatusCssClasses
     * @covers ::presentAppStatusCss
     * @covers ::getStatusCss
     */
    public function testPresentAppStatusCss($length, $missed, $expected)
    {
        $project = $this->mockProjectWithHealthStatus($length, $missed);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentAppStatusCss();

        $this->assertSame($expected, $actual);
    }

    public function provideAppStatusCssClasses()
    {
        return $this->fixture('View/Presenters/ProjectPresenter')['status_css_classes'];
    }

    /**
     * @covers ::presentHeartBeatStatus
     * @covers ::getStatusLabel
     */
    public function testPresentHeartbeatStatusReturnsMessageOnEmpty()
    {
        $expected = 'app.not_applicable';

        $project = $this->mockProjectWithHealthStatus(0, 0, 'heartbeatsStatus');

        $this->translator->shouldReceive('trans')->once()->with($expected)->andReturn($expected);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentHeartBeatStatus();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideHeartbeatStatuses
     * @covers ::presentHeartBeatStatus
     * @covers ::getStatusLabel
     */
    public function testPresentHeartbeatReturnsExpectedMessageWhenNotEmpty($length, $missed, $expected)
    {
        $project = $this->mockProjectWithHealthStatus($length, $missed, 'heartbeatsStatus');

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentHeartBeatStatus();

        $this->assertSame($expected, $actual);
    }

    public function provideHeartbeatStatuses()
    {
        return $this->provideAppStatuses();
    }

    /**
     * @dataProvider provideHeartbeatsCssClasses
     * @covers ::presentHeartBeatStatusCss
     * @covers ::getStatusCss
     */
    public function testPresentHeartbeatStatusCss($length, $missed, $expected)
    {
        $project = $this->mockProjectWithHealthStatus($length, $missed, 'heartbeatsStatus');

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentHeartBeatStatusCss();

        $this->assertSame($expected, $actual);
    }

    public function provideHeartbeatsCssClasses()
    {
        return $this->provideAppStatusCssClasses();
    }

    /**
     * @covers ::presentTypeIcon
     */
    public function testPresentTypeIconReturnsDefaultIcon()
    {
        $expected = 'fa-git-square';

        $project = m::mock(Project::class);
        $project->shouldReceive('accessDetails')->andReturnNull();

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentTypeIcon();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideRepoUrls
     * @covers ::presentTypeIcon
     */
    public function testPresentTypeIconReturnsExpectedIcon($repository, $expected)
    {
        $project = m::mock(Project::class);
        $project->shouldReceive('accessDetails')->andReturn(['domain' => $repository]);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentTypeIcon();

        $this->assertSame($expected, $actual);
    }

    public function provideRepoUrls()
    {
        return $this->fixture('View/Presenters/ProjectPresenter')['repo_icon'];
    }

    private function mockProjectWithStatus($status)
    {
        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->atLeast()->once()->with('status')->andReturn($status);

        return $project;
    }

    private function mockProjectWithHealthStatus($length, $missed, $method = 'applicationCheckUrlStatus')
    {
        $project = m::mock(Project::class);
        $project->shouldReceive($method)->once()->andReturn(['length' => $length, 'missed' => $missed]);

        return $project;
    }
}
