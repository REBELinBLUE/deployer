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

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = m::mock(Translator::class);
    }

    /**
     * @dataProvider provideCCTrayStatus
     * @covers ::presentCcTrayStatus
     *
     * @param mixed  $status
     * @param string $expected
     */
    public function testPresentCcTrayStatusIsCorrect($status, string $expected)
    {
        $project = $this->mockProjectWithStatus($status);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentCcTrayStatus();

        $this->assertSame($expected, $actual);
    }

    public function provideCCTrayStatus(): array
    {
        return $this->fixture('View/Presenters/ProjectPresenter')['cc_tray_status'];
    }

    /**
     * @dataProvider provideReadableStatus
     * @covers ::presentReadableStatus
     *
     * @param mixed  $status
     * @param string $expected
     */
    public function testPresentReadableStatusIsCorrect($status, string $expected)
    {
        $project = $this->mockProjectWithStatus($status);

        $this->translator->shouldReceive('get')->once()->with($expected)->andReturn($expected);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentReadableStatus();

        $this->assertSame($expected, $actual);
    }

    public function provideReadableStatus(): array
    {
        return $this->fixture('View/Presenters/ProjectPresenter')['readable_status'];
    }

    /**
     * @dataProvider provideIcons
     * @covers ::presentIcon
     *
     * @param mixed  $status
     * @param string $expected
     */
    public function testPresentIconIsCorrect($status, string $expected)
    {
        $project = $this->mockProjectWithStatus($status);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentIcon();

        $this->assertSame($expected, $actual);
    }

    public function provideIcons(): array
    {
        return $this->fixture('View/Presenters/ProjectPresenter')['icons'];
    }

    /**
     * @dataProvider provideCssClasses
     * @covers ::presentCssClass
     *
     * @param mixed  $status
     * @param string $expected
     */
    public function testPresentCssClassIsCorrect($status, string $expected)
    {
        $project = $this->mockProjectWithStatus($status);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentCssClass();

        $this->assertSame($expected, $actual);
    }

    public function provideCssClasses(): array
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

        $this->translator->shouldReceive('get')->once()->with($expected)->andReturn($expected);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentAppStatus();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideAppStatuses
     * @covers ::presentAppStatus
     * @covers ::getStatusLabel
     *
     * @param int    $length
     * @param int    $missed
     * @param string $expected
     */
    public function testPresentAppStatusReturnsExpectedMessageWhenNotEmpty(int $length, int $missed, string $expected)
    {
        $project = $this->mockProjectWithHealthStatus($length, $missed);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentAppStatus();

        $this->assertSame($expected, $actual);
    }

    public function provideAppStatuses(): array
    {
        return $this->fixture('View/Presenters/ProjectPresenter')['status_label'];
    }

    /**
     * @dataProvider provideAppStatusCssClasses
     * @covers ::presentAppStatusCss
     * @covers ::getStatusCss
     *
     * @param int    $length
     * @param int    $missed
     * @param string $expected
     */
    public function testPresentAppStatusCss(int $length, int $missed, string $expected)
    {
        $project = $this->mockProjectWithHealthStatus($length, $missed);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentAppStatusCss();

        $this->assertSame($expected, $actual);
    }

    public function provideAppStatusCssClasses(): array
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

        $this->translator->shouldReceive('get')->once()->with($expected)->andReturn($expected);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentHeartBeatStatus();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideHeartbeatStatuses
     * @covers ::presentHeartBeatStatus
     * @covers ::getStatusLabel
     *
     * @param int    $length
     * @param int    $missed
     * @param string $expected
     */
    public function testPresentHeartbeatReturnsExpectedMessageWhenNotEmpty(int $length, int $missed, string $expected)
    {
        $project = $this->mockProjectWithHealthStatus($length, $missed, 'heartbeatsStatus');

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentHeartBeatStatus();

        $this->assertSame($expected, $actual);
    }

    public function provideHeartbeatStatuses(): array
    {
        return $this->provideAppStatuses();
    }

    /**
     * @dataProvider provideHeartbeatsCssClasses
     * @covers ::presentHeartBeatStatusCss
     * @covers ::getStatusCss
     *
     * @param int    $length
     * @param int    $missed
     * @param string $expected
     */
    public function testPresentHeartbeatStatusCss(int $length, int $missed, string $expected)
    {
        $project = $this->mockProjectWithHealthStatus($length, $missed, 'heartbeatsStatus');

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentHeartBeatStatusCss();

        $this->assertSame($expected, $actual);
    }

    public function provideHeartbeatsCssClasses(): array
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
        $project->shouldReceive('accessDetails')->andReturn([]);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentTypeIcon();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideRepoUrls
     * @covers ::presentTypeIcon
     *
     * @param string $repository
     * @param string $expected
     */
    public function testPresentTypeIconReturnsExpectedIcon(?string $repository, string $expected)
    {
        $project = m::mock(Project::class);
        $project->shouldReceive('accessDetails')->andReturn(['domain' => $repository]);

        $presenter = new ProjectPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->presentTypeIcon();

        $this->assertSame($expected, $actual);
    }

    public function provideRepoUrls(): array
    {
        return $this->fixture('View/Presenters/ProjectPresenter')['repo_icon'];
    }

    /**
     * @param mixed $status
     *
     * @return Project
     */
    private function mockProjectWithStatus($status): Project
    {
        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->atLeast()->once()->with('status')->andReturn($status);

        return $project;
    }

    /**
     * @param int    $length
     * @param int    $missed
     * @param string $method
     *
     * @return Project
     */
    private function mockProjectWithHealthStatus(
        int $length,
        int $missed,
        string $method = 'applicationCheckUrlStatus'
    ): Project {
        $project = m::mock(Project::class);
        $project->shouldReceive($method)->once()->andReturn(['length' => $length, 'missed' => $missed]);

        return $project;
    }
}
