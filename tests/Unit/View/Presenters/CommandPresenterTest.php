<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Presenters;

use Illuminate\Contracts\Translation\Translator;
use Mockery as m;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Presenters\CommandPresenter;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Presenters\CommandPresenter
 */
class CommandPresenterTest extends TestCase
{
    private $translator;

    public function setUp()
    {
        parent::setUp();

        $this->translator = m::mock(Translator::class);
    }

    /**
     * @dataProvider provideMethods
     * @covers ::presentBeforeClone
     * @covers ::presentAfterClone
     * @covers ::presentBeforeInstall
     * @covers ::presentAfterInstall
     * @covers ::presentBeforeActivate
     * @covers ::presentAfterActivate
     * @covers ::presentBeforePurge
     * @covers ::presentAfterPurge
     * @covers ::commandNames
     */
    public function testPresentMethodsReturnTranslation($method)
    {
        $expected = 'app.none';

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->atLeast()->once()->with('commands')->andReturn([]);

        $this->translator->shouldReceive('trans')->once()->with($expected)->andReturn($expected);

        $presenter = new CommandPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->{$method}();

        $this->assertSame($expected, $actual, $method . ' did not translate');
    }

    /**
     * @dataProvider provideCommandsAndMethods
     * @covers ::presentBeforeClone
     * @covers ::presentAfterClone
     * @covers ::presentBeforeInstall
     * @covers ::presentAfterInstall
     * @covers ::presentBeforeActivate
     * @covers ::presentAfterActivate
     * @covers ::presentBeforePurge
     * @covers ::presentAfterPurge
     * @covers ::commandNames
     */
    public function testPresentMethodsReturnCommandNames($method, $expected, $commands)
    {
        $collection = [];

        foreach ($commands as $command) {
            $collection[] = $this->mockCommand($command[0], $command[1]);
        }
        $commands = collect($collection);

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->atLeast()->once()->with('commands')->andReturn($commands);

        $presenter = new CommandPresenter($this->translator);
        $presenter->setWrappedObject($project);
        $actual    = $presenter->{$method}();

        $this->assertSame($expected, $actual, $method . ' did not return expected names');
    }

    public function provideMethods()
    {
        return array_chunk([
            'presentBeforeClone', 'presentAfterClone', 'presentBeforeInstall', 'presentAfterInstall',
            'presentBeforeActivate', 'presentAfterActivate', 'presentBeforePurge', 'presentAfterPurge',
        ], 1);
    }

    public function provideCommandsAndMethods()
    {
        $data = [];
        foreach ($this->getSteps() as $step) {
            $method   = $step['method'];
            $expected = $step['before'];
            $other    = $step['after'];

            $data[] = [$method, 'step1', [
                ['step1', $expected], ],
            ];
            $data[] = [$method, 'step1', [
                ['step1', $expected], ['step2', $other],
            ]];
            $data[] = [$method, 'step1, step2', [
                ['step1', $expected], ['step2', $expected],
            ]];
            $data[] = [$method, 'step1, step3', [
                ['step1', $expected], ['step2', $other], ['step3', $expected],
            ]];
            $data[] = [$method, 'step1, step2, step3', [
                ['step1', $expected], ['step2', $expected], ['step3', $expected],
            ]];
        }

        return $data;
    }

    private function mockCommand($name, $step)
    {
        $command = m::mock(Command::class);
        $command->shouldReceive('getAttribute')->atLeast()->once()->with('step')->andReturn($step);
        $command->shouldReceive('getAttribute')->with('name')->andReturn($name);

        return $command;
    }

    private function getSteps()
    {
        return $this->fixture('View/Presenters/CommandPresenter')['steps'];
    }
}
