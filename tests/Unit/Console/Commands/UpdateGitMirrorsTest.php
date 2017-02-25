<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Carbon\Carbon;
use Closure;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\UpdateGitMirrors;
use REBELinBLUE\Deployer\Jobs\QueueUpdateGitMirror;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\UpdateGitMirrors
 */
class UpdateGitMirrorsTest extends TestCase
{
    private $repository;
    private $console;

    public function setUp()
    {
        parent::setUp();

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $repository = m::mock(ProjectRepositoryInterface::class);

        $this->repository = $repository;
        $this->console    = $console;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle()
    {
        $this->expectsJobs(QueueUpdateGitMirror::class);

        $now = Carbon::create(2017, 2, 5, 12, 45, 00, 'UTC');
        Carbon::setTestNow($now);

        $since = Carbon::create(2017, 2, 5, 12, 40, 00, 'UTC');

        $compareDate = m::on(function (Carbon $date) use ($since) {
            return $date->format('c') === $since->format('c');
        });

        $compareCallback = m::on(function ($callback) {
            $this->assertInstanceOf(Closure::class, $callback);

            $callback(collect([new Project()]));

            return true;
        });

        $this->repository->shouldReceive('getLastMirroredBefore')->with($compareDate, 3, $compareCallback);

        $command = new UpdateGitMirrors($this->repository);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:update-mirrors',
        ]);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleWithNoProjectsToUpdate()
    {
        $this->doesntExpectJobs(QueueUpdateGitMirror::class);

        $now = Carbon::create(2017, 2, 5, 12, 45, 00, 'UTC');
        Carbon::setTestNow($now);

        $since = Carbon::create(2017, 2, 5, 12, 40, 00, 'UTC');

        $compareDate = m::on(function (Carbon $date) use ($since) {
            return $date->format('c') === $since->format('c');
        });

        $compareCallback = m::on(function ($callback) {
            $this->assertInstanceOf(Closure::class, $callback);

            $callback(collect());

            return true;
        });

        $this->repository->shouldReceive('getLastMirroredBefore')->with($compareDate, 3, $compareCallback);

        $command = new UpdateGitMirrors($this->repository);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:update-mirrors',
        ]);
    }
}
