<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Carbon\Carbon;
use Closure;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\UpdateGitMirrors;
use REBELinBLUE\Deployer\Jobs\UpdateGitMirror;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\UpdateGitMirrors
 */
class UpdateGitMirrorsTest extends CommandTestCase
{
    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle()
    {
        $this->expectsJobs(UpdateGitMirror::class);

        $now = Carbon::create(2017, 2, 5, 12, 45, 00, 'UTC');
        Carbon::setTestNow($now);

        $since = Carbon::create(2017, 2, 5, 12, 40, 00, 'UTC');

        $compareDate = m::on(function (Carbon $date) use ($since) {
            return $date->format('c') === $since->format('c');
        });

        $repository = m::mock(ProjectRepositoryInterface::class);
        $repository->shouldReceive('getLastMirroredBefore')
                   ->with($compareDate, 3, m::on(function ($callback) {
                       $this->assertInstanceOf(Closure::class, $callback);

                       $callback(collect([new Project()]));

                       return true;
                   }));

        $command = new UpdateGitMirrors($repository);
        $command->setLaravel($this->app);

        $this->runCommand($command);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleWithNoProjectsToUpdate()
    {
        $this->doesntExpectJobs(UpdateGitMirror::class);

        $now = Carbon::create(2017, 2, 5, 12, 45, 00, 'UTC');
        Carbon::setTestNow($now);

        $since = Carbon::create(2017, 2, 5, 12, 40, 00, 'UTC');

        $compareDate = m::on(function (Carbon $date) use ($since) {
            return $date->format('c') === $since->format('c');
        });

        $repository = m::mock(ProjectRepositoryInterface::class);
        $repository->shouldReceive('getLastMirroredBefore')
            ->with($compareDate, 3, m::on(function ($callback) {
                $this->assertInstanceOf(Closure::class, $callback);

                $callback(collect());

                return true;
            }));

        $command = new UpdateGitMirrors($repository);
        $command->setLaravel($this->app);

        $this->runCommand($command);
    }
}
