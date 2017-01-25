<?php

namespace REBELinBLUE\Deployer\Tests\View\Presenters;

use Illuminate\Support\Facades\Lang;
use Mockery as m;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Presenters\DeploymentPresenter;

class DeploymentPresenterTest extends TestCase
{
    private function mockDeploymentWithStatus($status)
    {
        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('status')->andReturn($status);

        return $deployment;
    }

    /**
     * @dataProvider getCCTrayStatus
     */
    public function testPresentCcTrayStatusIsCorrect($status, $expected)
    {
        $deployment = $this->mockDeploymentWithStatus($status);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentCcTrayStatus();

        $this->assertEquals($expected, $actual);
    }

    public function getCCTrayStatus()
    {
        return [
            [Deployment::COMPLETED, 'Success'],
            [Deployment::COMPLETED_WITH_ERRORS, 'Success'],
            [Deployment::FAILED, 'Failure'],
            [Deployment::ABORTED, 'Failure'],
            [Deployment::PENDING, 'Unknown'],
            [Deployment::DEPLOYING, 'Unknown'],
            [Deployment::ABORTING, 'Unknown'],
            [Deployment::LOADING, 'Unknown'],
            ['invalid-value', 'Unknown'],
        ];
    }

    /**
     * @dataProvider getReadableStatus
     */
    public function testPresentReadableStatusIsCorrect($status, $expected)
    {
        $deployment = $this->mockDeploymentWithStatus($status);

        Lang::shouldReceive('get')->with($expected)->andReturn($expected);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentReadableStatus();

        $this->assertEquals($expected, $actual);
    }

    public function getReadableStatus()
    {
        return [
            [Deployment::COMPLETED, 'deployments.completed'],
            [Deployment::COMPLETED_WITH_ERRORS, 'deployments.completed_with_errors'],
            [Deployment::FAILED, 'deployments.failed'],
            [Deployment::ABORTED, 'deployments.aborted'],
            [Deployment::PENDING, 'deployments.pending'],
            [Deployment::DEPLOYING, 'deployments.deploying'],
            [Deployment::ABORTING, 'deployments.aborting'],
            [Deployment::LOADING, 'deployments.pending'],
            ['invalid-value', 'deployments.pending'],
        ];
    }

    /**
     * @dataProvider getIcons
     */
    public function testPresentIconIsCorrect($status, $expected)
    {
        $deployment = $this->mockDeploymentWithStatus($status);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentIcon();

        $this->assertEquals($expected, $actual);
    }

    public function getIcons()
    {
        return [
            [Deployment::COMPLETED, 'check'],
            [Deployment::COMPLETED_WITH_ERRORS, 'warning'],
            [Deployment::FAILED, 'warning'],
            [Deployment::ABORTED, 'warning'],
            [Deployment::PENDING, 'clock-o'],
            [Deployment::DEPLOYING, 'spinner fa-pulse'],
            [Deployment::ABORTING, 'warning'],
            [Deployment::LOADING, 'clock-o'],
            ['invalid-value', 'clock-o'],
        ];
    }

    /**
     * @dataProvider getCssClasses
     */
    public function testPresentCssClassIsCorrect($status, $expected)
    {
        $deployment = $this->mockDeploymentWithStatus($status);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentCssClass();

        $this->assertEquals($expected, $actual);
    }

    public function getCssClasses()
    {
        return [
            [Deployment::COMPLETED, 'success'],
            [Deployment::COMPLETED_WITH_ERRORS, 'success'],
            [Deployment::FAILED, 'danger'],
            [Deployment::ABORTED, 'danger'],
            [Deployment::PENDING, 'info'],
            [Deployment::DEPLOYING, 'warning'],
            [Deployment::ABORTING, 'danger'],
            [Deployment::LOADING, 'info'],
            ['invalid-value', 'info'],
        ];
    }

    /**
     * @dataProvider getTimelineCssClasses
     */
    public function testPresentTimelineCssClass($status, $expected)
    {
        $deployment = $this->mockDeploymentWithStatus($status);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentTimelineCssClass();

        $this->assertEquals($expected, $actual);
    }

    public function getTimelineCssClasses()
    {
        return [
            [Deployment::COMPLETED, 'green'],
            [Deployment::COMPLETED_WITH_ERRORS, 'green'],
            [Deployment::FAILED, 'red'],
            [Deployment::ABORTED, 'red'],
            [Deployment::PENDING, 'aqua'],
            [Deployment::DEPLOYING, 'yellow'],
            [Deployment::ABORTING, 'red'],
            [Deployment::LOADING, 'aqua'],
            ['invalid-value', 'aqua'],
        ];
    }

    public function testPresentCommitterNameReturnsName()
    {
        $expected = 'a-real-name';

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('committer')->andReturn($expected);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentCommitterName();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getCommiterName
     */
    public function testPresentCommitterNameReturnsTranslation($committer, $status, $expected)
    {
        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('committer')->andReturn($committer);
        $deployment->shouldReceive('getAttribute')->with('status')->andReturn($status);

        Lang::shouldReceive('get')->with($expected)->andReturn($expected);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentCommitterName();

        $this->assertEquals($expected, $actual);
    }

    public function getCommiterName()
    {
        return [
            [Deployment::LOADING, Deployment::LOADING, 'deployments.loading'],
            [Deployment::LOADING, Deployment::FAILED, 'deployments.unknown'],
        ];
    }

    public function testPresentShortCommitHashReturnsHash()
    {
        $expected = 'abcdedf';

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('short_commit')->andReturn($expected);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentShortCommitHash();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getShortHash
     */
    public function testPresentShortCommitHashReturnsTranslation($commit, $status, $expected)
    {
        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('short_commit')->andReturn($commit);
        $deployment->shouldReceive('getAttribute')->with('status')->andReturn($status);

        Lang::shouldReceive('get')->with($expected)->andReturn($expected);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentShortCommitHash();

        $this->assertEquals($expected, $actual);
    }

    public function getShortHash()
    {
        return [
            [Deployment::LOADING, Deployment::LOADING, 'deployments.loading'],
            [Deployment::LOADING, Deployment::FAILED, 'deployments.unknown'],
        ];
    }
}
