<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Carbon\Carbon;
use Closure;
use Mockery as m;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Console\Commands\CheckUrls;
use REBELinBLUE\Deployer\Jobs\RequestProjectCheckUrl;
use REBELinBLUE\Deployer\Repositories\Contracts\CheckUrlRepositoryInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\CheckUrls
 */
class CheckUrlsTest extends TestCase
{
    private $repository;
    private $console;

    public function setUp()
    {
        parent::setUp();

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $repository = m::mock(CheckUrlRepositoryInterface::class);

        $this->repository = $repository;
        $this->console    = $console;
    }

    /**
     * @dataProvider provideTimePeriods
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle($minute, $periods)
    {
        $this->expectsJobs(RequestProjectCheckUrl::class);

        Carbon::setTestNow(Carbon::create(2017, 2, 1, 15, $minute, 00, 'UTC'));

        $this->repository->shouldReceive('chunkWhereIn')
                         ->once()
                         ->with('period', $periods, CheckUrls::URLS_TO_CHECK, m::on(function ($callback) {
                             $this->assertInstanceOf(Closure::class, $callback);

                             $callback(collect([new CheckUrl()]));

                             return true;
                         }));

        $command = new CheckUrls($this->repository);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:checkurls',
        ]);
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

        $this->repository->shouldNotReceive('chunkWhereIn');

        $command = new CheckUrls($this->repository);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:checkurls',
        ]);
    }
}
