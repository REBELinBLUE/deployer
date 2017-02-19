<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Carbon\Carbon;
use Closure;
use Mockery as m;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Console\Commands\CheckUrls;
use REBELinBLUE\Deployer\Jobs\RequestProjectCheckUrl;
use REBELinBLUE\Deployer\Repositories\Contracts\CheckUrlRepositoryInterface;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\CheckUrls
 */
class CheckUrlsTest extends CommandTestCase
{
    /**
     * @dataProvider provideTimePeriods
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle($minute, $periods)
    {
        $this->expectsJobs(RequestProjectCheckUrl::class);

        Carbon::setTestNow(Carbon::create(2017, 2, 1, 15, $minute, 00, 'UTC'));

        $repository = m::mock(CheckUrlRepositoryInterface::class);

        $repository->shouldReceive('chunkWhereIn')
                   ->once()
                   ->with('period', $periods, CheckUrls::URLS_TO_CHECK, m::on(function ($callback) {
                       $this->assertInstanceOf(Closure::class, $callback);

                       $callback(collect([new CheckUrl()]));

                       return true;
                   }));

        $command = new CheckUrls($repository);
        $command->setLaravel($this->app);

        $this->runCommand($command);
    }

    public function provideTimePeriods()
    {
        return  [
            [55, [5]],
            [50, [10, 5]],
            [45, [5]],
            [40, [10, 5]],
            [35, [5]],
            [30, [30, 10, 5]],
            [25, [5]],
            [20, [10, 5]],
            [15, [5]],
            [10, [10, 5]],
            [05, [5]],
            [00, [60, 30, 10, 5]],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleAtUnexpectedTimes()
    {
        $this->doesntExpectJobs(RequestProjectCheckUrl::class);

        Carbon::setTestNow(Carbon::create(2017, 2, 1, 15, 04, 00, 'UTC'));

        $repository = m::mock(CheckUrlRepositoryInterface::class);

        $repository->shouldNotReceive('chunkWhereIn');

        $command = new CheckUrls($repository);
        $command->setLaravel($this->app);

        $this->runCommand($command);
    }
}
