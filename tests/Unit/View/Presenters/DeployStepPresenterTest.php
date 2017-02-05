<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Presenters;

use Illuminate\Support\Facades\Lang;
use Mockery as m;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Presenters\DeployStepPresenter;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Presenters\DeployStepPresenter
 */
class DeployStepPresenterTest extends TestCase
{
    /**
     * @covers ::presentName
     */
    public function testPresentNameReturnsCommandName()
    {
        $expected = 'some command';

        $command = m::mock(Command::class);
        $command->shouldReceive('getAttribute')->atLeast()->times(1)->with('name')->andReturn($expected);

        $step = m::mock(DeployStep::class);
        $step->shouldReceive('getAttribute')->atLeast()->times(1)->with('command_id')->andReturn(1);
        $step->shouldReceive('getAttribute')->atLeast()->times(1)->with('command')->andReturn($command);

        $presenter = new DeployStepPresenter($step);
        $actual    = $presenter->presentName();

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideStageLabels
     * @covers ::presentName
     */
    public function testPresentNameReturnsLabel($stage, $expected)
    {
        $step = m::mock(DeployStep::class);
        $step->shouldReceive('getAttribute')->atLeast()->times(1)->with('command_id')->andReturnNull();
        $step->shouldReceive('getAttribute')->atLeast()->times(1)->with('stage')->andReturn($stage);

        Lang::shouldReceive('get')->once()->with($expected)->andReturn($expected);

        $presenter = new DeployStepPresenter($step);
        $actual    = $presenter->presentName();

        $this->assertSame($expected, $actual);
    }

    public function provideStageLabels()
    {
        return $this->fixture('View/Presenters/DeployStepPresenter')['stages'];
    }
}
