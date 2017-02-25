<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\DownCommand;
use Illuminate\Foundation\Console\UpCommand;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\ClearStalledDeployment;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerLogRepositoryInterface;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\ClearStalledDeployment
 * @todo: mock laravel so that the isDownForMaintenance branch can be tested
 */
class ClearStalledDeploymentTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::cleanupDeployments
     */
    public function testHandle()
    {
        $log = m::mock(ServerLogRepositoryInterface::class);
        $log->shouldReceive('updateStatusAll')->once()->with(ServerLog::PENDING, ServerLog::CANCELLED);
        $log->shouldReceive('updateStatusAll')->once()->with(ServerLog::RUNNING, ServerLog::FAILED);

        $deployment = m::mock(DeploymentRepositoryInterface::class);
        $deployment->shouldReceive('updateStatusAll')->once()->with(Deployment::DEPLOYING, Deployment::FAILED);
        $deployment->shouldReceive('updateStatusAll')->once()->with(Deployment::PENDING, Deployment::FAILED);
        $deployment->shouldReceive('updateStatusAll')->once()->with(Deployment::ABORTING, Deployment::ABORTED);

        $project = m::mock(ProjectRepositoryInterface::class);
        $project->shouldReceive('updateStatusAll')->once()->with(Project::DEPLOYING, Project::FAILED);
        $project->shouldReceive('updateStatusAll')->once()->with(Project::PENDING, Project::FAILED);

        $down = m::mock(DownCommand::class);
        $down->shouldReceive('run');

        $up = m::mock(UpCommand::class);
        $up->shouldReceive('run');

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();
        $console->shouldReceive('find')->with('down')->andReturn($down);
        $console->shouldReceive('find')->with('up')->andReturn($up);

        $app = m::mock(Application::class)->makePartial();
        $app->shouldReceive('isDownForMaintenance')->andReturn(false);

        $command = new ClearStalledDeployment($log, $deployment, $project);
        $command->setLaravel($app);
        $command->setApplication($console);

        $tester = new CommandTester($command);
        $tester->setInputs(['yes']);
        $tester->execute([
            'command' => 'deployer:cleanup',
        ]);

        $this->assertContains('Switch to maintenance mode now?', $tester->getDisplay());
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::cleanupDeployments
     */
    public function testHandleWhenConfirmationDenied()
    {
        $log = m::mock(ServerLogRepositoryInterface::class);
        $log->shouldNotReceive('updateStatusAll');

        $deployment = m::mock(DeploymentRepositoryInterface::class);
        $deployment->shouldNotReceive('updateStatusAll');

        $project = m::mock(ProjectRepositoryInterface::class);
        $project->shouldNotReceive('updateStatusAll');

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();
        $console->shouldNotReceive('find')->with('down');
        $console->shouldNotReceive('find')->with('up');

        $app = m::mock(Application::class)->makePartial();
        $app->shouldReceive('isDownForMaintenance')->andReturn(false);

        $command = new ClearStalledDeployment($log, $deployment, $project);
        $command->setLaravel($app);
        $command->setApplication($console);

        $tester = new CommandTester($command);
        $tester->setInputs(['no']);
        $tester->execute([
            'command' => 'deployer:cleanup',
        ]);

        $this->assertContains('Switch to maintenance mode now?', $tester->getDisplay());
    }
}
