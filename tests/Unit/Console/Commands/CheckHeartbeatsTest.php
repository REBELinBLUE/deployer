<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Carbon\Carbon;
use Closure;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\CheckHeartbeats;
use REBELinBLUE\Deployer\Events\HeartbeatMissed;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Repositories\Contracts\HeartbeatRepositoryInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\CheckHeartbeats
 */
class CheckHeartbeatsTest extends TestCase
{
    private $repository;
    private $console;

    public function setUp()
    {
        parent::setUp();

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $repository = m::mock(HeartbeatRepositoryInterface::class);

        $this->repository = $repository;
        $this->console    = $console;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle()
    {
        $this->expectsEvents(HeartbeatMissed::class);

        Carbon::setTestNow(Carbon::create(2017, 2, 1, 15, 00, 00, 'UTC'));

        $this->repository->shouldReceive('chunk')->once()->with(10, m::on(function ($callback) {
            $this->assertInstanceOf(Closure::class, $callback);

            $heartbeat = $this->mockHeartbeat();
            $heartbeat->shouldReceive('setAttribute')->once()->with('status', Heartbeat::MISSING);
            $heartbeat->shouldReceive('setAttribute')->once()->with('missed', 1);
            $heartbeat->shouldReceive('save')->once();

            $callback(collect([$heartbeat]));

            return true;
        }));

        $command = new CheckHeartbeats($this->repository);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:heartbeats',
        ]);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleWhenNotOutdated()
    {
        $this->doesntExpectEvents(HeartbeatMissed::class);

        Carbon::setTestNow(Carbon::create(2017, 1, 1, 15, 50, 00, 'UTC'));

        $this->repository->shouldReceive('chunk')->once()->with(10, m::on(function ($callback) {
            $this->assertInstanceOf(Closure::class, $callback);

            $heartbeat = $this->mockHeartbeat();
            $heartbeat->shouldNotReceive('setAttribute')->with('status', m::type('int'));
            $heartbeat->shouldNotReceive('setAttribute')->with('missed', m::type('int'));
            $heartbeat->shouldNotReceive('save');

            $callback(collect([$heartbeat]));

            return true;
        }));

        $command = new CheckHeartbeats($this->repository);
        $command->setLaravel($this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'deployer:heartbeats',
        ]);
    }

    private function mockHeartbeat()
    {
        $created_at = Carbon::create(2017, 1, 1, 15, 45, 35, 'UTC');

        $heartbeat = m::mock(Heartbeat::class);
        $heartbeat->shouldReceive('getAttribute')->with('last_activity')->andReturnNull();
        $heartbeat->shouldReceive('getAttribute')->with('created_at')->andReturn($created_at);
        $heartbeat->shouldReceive('getAttribute')->with('missed')->andReturn(0);
        $heartbeat->shouldReceive('getAttribute')->with('interval')->andReturn(30);

        return $heartbeat;
    }
}
