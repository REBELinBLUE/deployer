<?php

namespace REBELinBLUE\Deployer\Tests\View\Presenters;

use Illuminate\Support\Facades\Lang;
use Mockery as m;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Presenters\ProjectPresenter;

// TODO: Refactor the health check status and CSS methods as lots of duplication
class ProjectPresenterTest extends TestCase
{
    private function mockProjectWithStatus($status)
    {
        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->with('status')->andReturn($status);

        return $project;
    }

    private function mockProjectWithHealthStatus($length, $missed, $method = 'applicationCheckUrlStatus')
    {
        $project = m::mock(Project::class);
        $project->shouldReceive($method)->andReturn(['length' => $length, 'missed' => $missed]);

        return $project;
    }

    /**
     * @dataProvider getCCTrayStatus
     */
    public function testPresentCcTrayStatusIsCorrect($status, $expected)
    {
        $project = $this->mockProjectWithStatus($status);

        $presenter = new ProjectPresenter($project);
        $actual    = $presenter->presentCcTrayStatus();

        $this->assertEquals($expected, $actual);
    }

    public function getCCTrayStatus()
    {
        return [
            [Project::FINISHED, 'Sleeping'],
            [Project::FAILED, 'Sleeping'],
            [Project::DEPLOYING, 'Building'],
            [Project::PENDING, 'Pending'],
            [Project::NOT_DEPLOYED, 'Unknown'],
            ['invalid-value', 'Unknown'],
        ];
    }

    /**
     * @dataProvider getReadableStatus
     */
    public function testPresentReadableStatusIsCorrect($status, $expected)
    {
        $project = $this->mockProjectWithStatus($status);

        Lang::shouldReceive('get')->with($expected)->andReturn($expected);

        $presenter = new ProjectPresenter($project);
        $actual    = $presenter->presentReadableStatus();

        $this->assertEquals($expected, $actual);
    }

    public function getReadableStatus()
    {
        return [
            [Project::FINISHED, 'projects.finished'],
            [Project::DEPLOYING, 'projects.deploying'],
            [Project::FAILED, 'projects.failed'],
            [Project::PENDING, 'projects.pending'],
            [Project::NOT_DEPLOYED, 'projects.not_deployed'],
            ['invalid-value', 'projects.not_deployed'],
        ];
    }

    /**
     * @dataProvider getIcons
     */
    public function testPresentIconIsCorrect($status, $expected)
    {
        $project = $this->mockProjectWithStatus($status);

        $presenter = new ProjectPresenter($project);
        $actual    = $presenter->presentIcon();

        $this->assertEquals($expected, $actual);
    }

    public function getIcons()
    {
        return [
            [Project::FINISHED, 'check'],
            [Project::DEPLOYING, 'spinner fa-pulse'],
            [Project::FAILED, 'warning'],
            [Project::PENDING, 'clock-o'],
            [Project::NOT_DEPLOYED, 'question-circle'],
            ['invalid-value', 'question-circle'],
        ];
    }

    /**
     * @dataProvider getCssClasses
     */
    public function testPresentCssClassIsCorrect($status, $expected)
    {
        $project = $this->mockProjectWithStatus($status);

        $presenter = new ProjectPresenter($project);
        $actual    = $presenter->presentCssClass();

        $this->assertEquals($expected, $actual);
    }

    public function getCssClasses()
    {
        return [
            [Project::FINISHED, 'success'],
            [Project::DEPLOYING, 'warning'],
            [Project::FAILED, 'danger'],
            [Project::PENDING, 'info'],
            [Project::NOT_DEPLOYED, 'primary'],
            ['invalid-value', 'primary'],
        ];
    }

    public function testPresentAppStatusReturnsMessageOnEmpty()
    {
        $expected = 'app.not_applicable';

        $project = $this->mockProjectWithHealthStatus(0, 0);

        Lang::shouldReceive('get')->with($expected)->andReturn($expected);

        $presenter = new ProjectPresenter($project);
        $actual    = $presenter->presentAppStatus();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getAppStatuses
     */
    public function testPresentAppStatusReturnsExpectedMessageWhenNotEmpty($length, $missed, $expected)
    {
        $project = $this->mockProjectWithHealthStatus($length, $missed);

        $presenter = new ProjectPresenter($project);
        $actual    = $presenter->presentAppStatus();

        $this->assertEquals($expected, $actual);
    }

    public function getAppStatuses()
    {
        return [
            [1, 0, '1 / 1'],
            [1, 1, '0 / 1'],
            [2, 1, '1 / 2'],
            [2, 2, '0 / 2'],
        ];
    }

    /**
     * @dataProvider getAppStatusCssClasses
     */
    public function testPresentAppStatusCss($length, $missed, $expected)
    {
        $project = $this->mockProjectWithHealthStatus($length, $missed);

        $presenter = new ProjectPresenter($project);
        $actual    = $presenter->presentAppStatusCss();

        $this->assertEquals($expected, $actual);
    }

    public function getAppStatusCssClasses()
    {
        return [
            [0, 0, 'warning'],
            [0, 1, 'warning'],
            [0, 2, 'warning'],
            [1, 0, 'success'],
            [2, 0, 'success'],
            [1, 1, 'danger'],
        ];
    }

    public function testPresentHeartbeatStatusReturnsMessageOnEmpty()
    {
        $expected = 'app.not_applicable';

        $project = $this->mockProjectWithHealthStatus(0, 0, 'heartbeatsStatus');

        Lang::shouldReceive('get')->with($expected)->andReturn($expected);

        $presenter = new ProjectPresenter($project);
        $actual    = $presenter->presentHeartBeatStatus();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getHeartbeatStatuses
     */
    public function testPresentHeartbeatReturnsExpectedMessageWhenNotEmpty($length, $missed, $expected)
    {
        $project = $this->mockProjectWithHealthStatus($length, $missed, 'heartbeatsStatus');

        $presenter = new ProjectPresenter($project);
        $actual    = $presenter->presentHeartBeatStatus();

        $this->assertEquals($expected, $actual);
    }

    public function getHeartbeatStatuses()
    {
        return [
            [1, 0, '1 / 1'],
            [1, 1, '0 / 1'],
            [2, 1, '1 / 2'],
            [2, 2, '0 / 2'],
        ];
    }

    /**
     * @dataProvider getHeartbeatsCssClasses
     */
    public function testPresentHeartbeatStatusCss($length, $missed, $expected)
    {
        $project = $this->mockProjectWithHealthStatus($length, $missed, 'heartbeatsStatus');

        $presenter = new ProjectPresenter($project);
        $actual    = $presenter->presentHeartBeatStatusCss();

        $this->assertEquals($expected, $actual);
    }

    public function getHeartbeatsCssClasses()
    {
        return [
            [0, 0, 'warning'],
            [0, 1, 'warning'],
            [0, 2, 'warning'],
            [1, 0, 'success'],
            [2, 0, 'success'],
            [1, 1, 'danger'],
        ];
    }

    public function testPresentTypeIconReturnsDefaultIcon()
    {
        $expected = 'fa-git-square';

        $project = m::mock(Project::class);
        $project->shouldReceive('accessDetails')->andReturnNull();

        $presenter = new ProjectPresenter($project);
        $actual    = $presenter->presentTypeIcon();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getRepoUrls
     */
    public function testPresentTypeIconReturnsExpectedIcon($repository, $expected)
    {
        $project = m::mock(Project::class);
        $project->shouldReceive('accessDetails')->andReturn(['domain' => $repository]);

        $presenter = new ProjectPresenter($project);
        $actual    = $presenter->presentTypeIcon();

        $this->assertEquals($expected, $actual);
    }

    public function getRepoUrls()
    {
        return [
            ['https://github.com/REBELinBLUE/deployer.git', 'fa-github'],
            ['ssh://github@github.com:REBELinBLUE/deployer.git', 'fa-github'],
            ['https://gitlab.com/REBELinBLUE/deployer.git', 'fa-gitlab'],
            ['ssh://gitlab@gitlab.com:REBELinBLUE/deployer.git', 'fa-gitlab'],
            ['https://bitbucket.org/rebelinblue/deployer.git', 'fa-bitbucket'],
            ['ssh://git@bitbucket.org:rebelinblue/deployer.git', 'fa-bitbucket'],
            ['https://git-codecommit.us-east-2.amazonaws.com/v1/repos/deployer.git', 'fa-amazon'],
            ['ssh://key@git-codecommit.us-east-2.amazonaws.com/v1/repos/deployer', 'fa-amazon'],
            ['www.invalid.url.com', 'fa-git-square'],
            [null, 'fa-git-square'],
        ];
    }
}
