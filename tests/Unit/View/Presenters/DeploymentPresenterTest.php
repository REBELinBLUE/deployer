<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Presenters;

use Illuminate\Support\Facades\Lang;
use Mockery as m;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Presenters\DeploymentPresenter;
use RuntimeException;
use stdClass;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Presenters\DeploymentPresenter
 */
class DeploymentPresenterTest extends TestCase
{
    /**
     * @covers ::presentReadableRuntime
     */
    public function testRuntimeInterfaceIsUsed()
    {
        $this->expectException(RuntimeException::class);

        // Class which doesn't implement the RuntimeInterface
        $presenter = new DeploymentPresenter(new stdClass());
        $presenter->presentReadableRuntime();
    }

    /**
     * @dataProvider provideCCTrayStatus
     * @covers ::presentCcTrayStatus
     */
    public function testPresentCcTrayStatusIsCorrect($status, $expected)
    {
        $deployment = $this->mockDeploymentWithStatus($status);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentCcTrayStatus();

        $this->assertSame($expected, $actual);
    }

    public function provideCCTrayStatus()
    {
        return $this->fixture('View/Presenters/DeploymentPresenter')['cc_tray_status'];
    }

    /**
     * @dataProvider provideReadableStatus
     * @covers ::presentReadableStatus
     */
    public function testPresentReadableStatusIsCorrect($status, $expected)
    {
        $deployment = $this->mockDeploymentWithStatus($status);

        Lang::shouldReceive('get')->once()->with($expected)->andReturn($expected);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentReadableStatus();

        $this->assertSame($expected, $actual);
    }

    public function provideReadableStatus()
    {
        return $this->fixture('View/Presenters/DeploymentPresenter')['readable_status'];
    }

    /**
     * @dataProvider provideIcons
     * @covers ::presentIcon
     */
    public function testPresentIconIsCorrect($status, $expected)
    {
        $deployment = $this->mockDeploymentWithStatus($status);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentIcon();

        $this->assertSame($expected, $actual);
    }

    public function provideIcons()
    {
        return $this->fixture('View/Presenters/DeploymentPresenter')['icons'];
    }

    /**
     * @dataProvider provideCssClasses
     * @covers ::presentCssClass
     */
    public function testPresentCssClassIsCorrect($status, $expected)
    {
        $deployment = $this->mockDeploymentWithStatus($status);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentCssClass();

        $this->assertSame($expected, $actual);
    }

    public function provideCssClasses()
    {
        return $this->fixture('View/Presenters/DeploymentPresenter')['css_classes'];
    }

    /**
     * @dataProvider provideTimelineCssClasses
     * @covers ::presentTimelineCssClass
     */
    public function testPresentTimelineCssClass($status, $expected)
    {
        $deployment = $this->mockDeploymentWithStatus($status);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentTimelineCssClass();

        $this->assertSame($expected, $actual);
    }

    public function provideTimelineCssClasses()
    {
        return $this->fixture('View/Presenters/DeploymentPresenter')['timeline_css_classes'];
    }

    /**
     * @covers ::presentCommitterName
     */
    public function testPresentCommitterNameReturnsName()
    {
        $expected = 'a-real-name';

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->times(1)->with('committer')->andReturn($expected);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentCommitterName();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideCommiterName
     * @covers ::presentCommitterName
     */
    public function testPresentCommitterNameReturnsTranslation($committer, $status, $expected)
    {
        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->times(1)->with('committer')->andReturn($committer);
        $deployment->shouldReceive('getAttribute')->atLeast()->times(1)->with('status')->andReturn($status);

        Lang::shouldReceive('get')->once()->with($expected)->andReturn($expected);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentCommitterName();

        $this->assertSame($expected, $actual);
    }

    public function provideCommiterName()
    {
        return $this->fixture('View/Presenters/DeploymentPresenter')['committer_name'];
    }

    /**
     * @covers ::presentShortCommitHash
     */
    public function testPresentShortCommitHashReturnsHash()
    {
        $expected = 'abcdedf';

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->times(1)->with('short_commit')->andReturn($expected);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentShortCommitHash();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideShortHash
     * @covers ::presentShortCommitHash
     */
    public function testPresentShortCommitHashReturnsTranslation($commit, $status, $expected)
    {
        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->times(1)->with('short_commit')->andReturn($commit);
        $deployment->shouldReceive('getAttribute')->atLeast()->times(1)->with('status')->andReturn($status);

        Lang::shouldReceive('get')->once()->with($expected)->andReturn($expected);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentShortCommitHash();

        $this->assertSame($expected, $actual);
    }

    public function provideShortHash()
    {
        // FIXME: Is this right?
        return $this->fixture('View/Presenters/DeploymentPresenter')['short_hash_translations'];
    }

    /**
     * @dataProvider provideCommandsUsed
     * @covers ::presentOptionalCommandsUsed
     */
    public function testPresentOptionalCommandsUsed(array $commands, $expected)
    {
        $collection = [];
        foreach ($commands as $id => $optional) {
            $collection[] = $this->mockCommand($id + 1, $optional);
        }
        $commands = collect($collection);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->times(1)->with('commands')->andReturn($commands);

        $presenter = new DeploymentPresenter($deployment);
        $actual    = $presenter->presentOptionalCommandsUsed();

        $this->assertSame($expected, $actual);
    }

    public function provideCommandsUsed()
    {
        return $this->fixture('View/Presenters/DeploymentPresenter')['commands_used'];
    }

    private function mockDeploymentWithStatus($status)
    {
        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->times(1)->with('status')->andReturn($status);

        return $deployment;
    }

    private function mockCommand($id, $optional = false)
    {
        $command = m::mock(Command::class);
        $command->shouldReceive('getAttribute')->atLeast()->times(1)->with('optional')->andReturn($optional);

        if ($optional) {
            $command->shouldReceive('offsetExists')->atLeast()->times(1)->with('id')->andReturn(true);
            $command->shouldReceive('offsetGet')->atLeast()->times(1)->with('id')->andReturn($id);
        }

        return $command;
    }
}
