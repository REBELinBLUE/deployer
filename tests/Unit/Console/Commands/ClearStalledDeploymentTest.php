<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Illuminate\Console\Application;
use Illuminate\Console\Command;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\ClearStalledDeployment;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerLogRepositoryInterface;
use REBELinBLUE\Deployer\ServerLog;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\ClearStalledDeployment
 */
class ClearStalledDeploymentTest extends CommandTestCase
{
    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::cleanupDeployments
     */
    public function testHandle()
    {
        $this->markTestSkipped('Not yet working');
//        $this->expectsJobs(RequestProjectCheckUrl::class);
//
//        Carbon::setTestNow(Carbon::create(2017, 2, 1, 15, $minute, 00, 'UTC'));
//
//        $repository = m::mock(CheckUrlRepositoryInterface::class);
//
//        $repository->shouldReceive('chunkWhereIn')
//            ->once()
//            ->with('period', $periods, CheckUrls::URLS_TO_CHECK, m::on(function ($callback) {
//                $this->assertInstanceOf(Closure::class, $callback);
//
//                $callback(collect([new CheckUrl()]));
//
//                return true;
//            }));

        $application = m::mock(Application::class);
        $application->shouldReceive('call')->once()->with('down');
        $application->shouldReceive('call')->once()->with('up');

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

        $command = new ClearStalledDeployment($log, $deployment, $project);
        $command->setLaravel($application);

//        $tester = new CommandTester($command);
//        $tester->setInputs(['yes']);
//        $tester->execute([]);

        $output = $this->runCommand($command);
    }

    protected function runCommand(Command $command, $input = [])
    {
        $output = m::mock(OutputInterface::class);
        $output->shouldReceive('getVerbosity');
        $output->shouldReceive('getFormatter');
        $output->shouldReceive('confirm')->with(m::type('string'))->andReturn(true);

        $input = new ArrayInput($input);

        $command->run($input, $output);

        return $output->fetch();
    }
}
