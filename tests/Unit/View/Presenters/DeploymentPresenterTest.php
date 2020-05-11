<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Presenters;

use Illuminate\Contracts\Translation\Translator;
use Mockery as m;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;
use REBELinBLUE\Deployer\View\Presenters\DeploymentPresenter;
use RuntimeException;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Presenters\DeploymentPresenter
 */
class DeploymentPresenterTest extends TestCase
{
    private $translator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = m::mock(Translator::class);
    }

    /**
     * @covers ::presentReadableRuntime
     */
    public function testRuntimeInterfaceIsUsed()
    {
        $this->expectException(RuntimeException::class);

        $invalid = new User();

        // Class which doesn't implement the RuntimeInterface
        $presenter = new DeploymentPresenter($this->translator);
        $presenter->setWrappedObject($invalid);
        $presenter->presentReadableRuntime();
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
        $deployment = $this->mockDeploymentWithStatus($status);

        $presenter = new DeploymentPresenter($this->translator);
        $presenter->setWrappedObject($deployment);
        $actual    = $presenter->presentCcTrayStatus();

        $this->assertSame($expected, $actual);
    }

    public function provideCCTrayStatus(): array
    {
        return $this->fixture('View/Presenters/DeploymentPresenter')['cc_tray_status'];
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
        $deployment = $this->mockDeploymentWithStatus($status);

        $this->translator->shouldReceive('get')->once()->with($expected)->andReturn($expected);

        $presenter = new DeploymentPresenter($this->translator);
        $presenter->setWrappedObject($deployment);
        $actual    = $presenter->presentReadableStatus();

        $this->assertSame($expected, $actual);
    }

    public function provideReadableStatus(): array
    {
        return $this->fixture('View/Presenters/DeploymentPresenter')['readable_status'];
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
        $deployment = $this->mockDeploymentWithStatus($status);

        $presenter = new DeploymentPresenter($this->translator);
        $presenter->setWrappedObject($deployment);
        $actual    = $presenter->presentIcon();

        $this->assertSame($expected, $actual);
    }

    public function provideIcons(): array
    {
        return $this->fixture('View/Presenters/DeploymentPresenter')['icons'];
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
        $deployment = $this->mockDeploymentWithStatus($status);

        $presenter = new DeploymentPresenter($this->translator);
        $presenter->setWrappedObject($deployment);
        $actual    = $presenter->presentCssClass();

        $this->assertSame($expected, $actual);
    }

    public function provideCssClasses(): array
    {
        return $this->fixture('View/Presenters/DeploymentPresenter')['css_classes'];
    }

    /**
     * @dataProvider provideTimelineCssClasses
     * @covers ::presentTimelineCssClass
     *
     * @param mixed  $status
     * @param string $expected
     */
    public function testPresentTimelineCssClass($status, string $expected)
    {
        $deployment = $this->mockDeploymentWithStatus($status);

        $presenter = new DeploymentPresenter($this->translator);
        $presenter->setWrappedObject($deployment);
        $actual    = $presenter->presentTimelineCssClass();

        $this->assertSame($expected, $actual);
    }

    public function provideTimelineCssClasses(): array
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
        $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('committer')->andReturn($expected);

        $presenter = new DeploymentPresenter($this->translator);
        $presenter->setWrappedObject($deployment);
        $actual    = $presenter->presentCommitterName();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideCommiterName
     * @covers ::presentCommitterName
     *
     * @param string $committer
     * @param mixed  $status
     * @param string $expected
     */
    public function testPresentCommitterNameReturnsTranslation(string $committer, $status, string $expected)
    {
        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('committer')->andReturn($committer);
        $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('status')->andReturn($status);

        $this->translator->shouldReceive('get')->once()->with($expected)->andReturn($expected);

        $presenter = new DeploymentPresenter($this->translator);
        $presenter->setWrappedObject($deployment);
        $actual    = $presenter->presentCommitterName();

        $this->assertSame($expected, $actual);
    }

    public function provideCommiterName(): array
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
        $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('short_commit')->andReturn($expected);

        $presenter = new DeploymentPresenter($this->translator);
        $presenter->setWrappedObject($deployment);
        $actual    = $presenter->presentShortCommitHash();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideShortHash
     * @covers ::presentShortCommitHash
     *
     * @param string $commit
     * @param mixed  $status
     * @param string $expected
     */
    public function testPresentShortCommitHashReturnsTranslation(string $commit, $status, string $expected)
    {
        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('short_commit')->andReturn($commit);
        $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('status')->andReturn($status);

        $this->translator->shouldReceive('get')->once()->with($expected)->andReturn($expected);

        $presenter = new DeploymentPresenter($this->translator);
        $presenter->setWrappedObject($deployment);
        $actual    = $presenter->presentShortCommitHash();

        $this->assertSame($expected, $actual);
    }

    public function provideShortHash(): array
    {
        return $this->fixture('View/Presenters/DeploymentPresenter')['short_hash_translations'];
    }

    /**
     * @dataProvider provideCommandsUsed
     * @covers ::presentOptionalCommandsUsed
     *
     * @param array  $commands
     * @param string $expected
     */
    public function testPresentOptionalCommandsUsed(array $commands, string $expected)
    {
        $collection = [];
        foreach ($commands as $id => $optional) {
            $collection[] = $this->mockCommand($id + 1, $optional);
        }
        $commands = collect($collection);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('commands')->andReturn($commands);

        $presenter = new DeploymentPresenter($this->translator);
        $presenter->setWrappedObject($deployment);
        $actual    = $presenter->presentOptionalCommandsUsed();

        $this->assertSame($expected, $actual);
    }

    public function provideCommandsUsed(): array
    {
        return $this->fixture('View/Presenters/DeploymentPresenter')['commands_used'];
    }

    /**
     * @param mixed $status
     *
     * @return Deployment
     */
    private function mockDeploymentWithStatus($status): Deployment
    {
        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('status')->andReturn($status);

        return $deployment;
    }

    /**
     * @param int  $command_id
     * @param bool $optional
     *
     * @return Command
     */
    private function mockCommand(int $command_id, bool $optional = false): Command
    {
        $command = m::mock(Command::class);
        $command->shouldReceive('getAttribute')->atLeast()->once()->with('optional')->andReturn($optional);

        if ($optional) {
            $command->shouldReceive('offsetExists')->atLeast()->once()->with('id')->andReturn(true);
            $command->shouldReceive('offsetGet')->atLeast()->once()->with('id')->andReturn($command_id);
        }

        return $command;
    }
}
