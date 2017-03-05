<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events;

use Carbon\Carbon;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use McCool\LaravelAutoPresenter\AutoPresenter;
use Mockery as m;
use REBELinBLUE\Deployer\Events\ServerLogChanged;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Presenters\ServerLogPresenter;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\ServerLogChanged
 */
class ServerLogChangedTest extends TestCase
{
    private $presenter;
    private $decorator;

    public function setUp()
    {
        parent::setUp();

        $this->presenter = m::mock(ServerLogPresenter::class);

        $this->decorator = m::mock(AutoPresenter::class);
        $this->decorator->shouldReceive('decorate')->andReturn($this->presenter);
    }

    /**
     * @covers ::broadcastOn
     */
    public function testBroadcastOn()
    {
        $event = new ServerLogChanged($this->mockServerlog(1, ''), $this->decorator);
        $this->assertSame(['serverlog'], $event->broadcastOn());
    }

    /**
     * @covers ::__construct
     */
    public function testIsBroadcastable()
    {
        $event = new ServerLogChanged($this->mockServerlog(10, ''), $this->decorator);
        $this->assertInstanceOf(ShouldBroadcast::class, $event);
    }

    /**
     * @covers ::__construct
     */
    public function testAttributesAreSet()
    {
        $server_id = 1000;
        $status    = ServerLog::COMPLETED;

        $event = new ServerLogChanged(
            $this->mockServerlog($server_id, 'output-log', $status),
            $this->decorator
        );

        $this->assertSame($server_id, $event->log_id);
        $this->assertSame($status, $event->status);
        $this->assertEmpty($event->output);
        $this->assertNull($event->started_at);
        $this->assertNull($event->finished_at);
        $this->assertNull($event->runtime);
    }

    /**
     * @covers ::__construct
     */
    public function testEmptyLogReturnsNull()
    {
        $event = new ServerLogChanged(
            $this->mockServerlog(1, '', ServerLog::COMPLETED),
            $this->decorator
        );

        $this->assertNull($event->output);
    }

    /**
     * @covers ::__construct
     */
    public function testTimestampsReturnStringWhenNotNull()
    {
        $started = Carbon::create(2016, 1, 1, 12, 25, 00, 'UTC');
        $ended   = Carbon::create(2016, 1, 1, 12, 32, 15, 'UTC');

        $event = new ServerLogChanged(
            $this->mockServerlog(1, '', ServerLog::COMPLETED, $started, $ended),
            $this->decorator
        );

        $this->assertSame('2016-01-01 12:25:00', $event->started_at);
        $this->assertSame('2016-01-01 12:32:15', $event->finished_at);
    }

    /**
     * @covers ::__construct
     */
    public function testRunTimeFormatted()
    {
        $expected = 'a-formatted-runtime-string';

        $started = Carbon::create(2016, 1, 1, 12, 25, 00, 'UTC');
        $ended   = Carbon::create(2016, 1, 1, 12, 32, 15, 'UTC');

        $this->presenter->shouldReceive('presentReadableRuntime')->andReturn($expected);

        $event = new ServerLogChanged(
            $this->mockServerlog(1, '', ServerLog::COMPLETED, $started, $ended, 735),
            $this->decorator
        );

        $this->assertSame($expected, $event->runtime);
    }

    private function mockServerlog(
        $log_id,
        $output,
        $status = '',
        $started_at = null,
        $finished_at = null,
        $runtime = false
    ) {
        $log = m::mock(ServerLog::class);
        $log->shouldDeferMissing();
        $log->shouldReceive('getAttribute')->once()->with('id')->andReturn($log_id);
        $log->shouldReceive('getAttribute')->atLeast()->once()->with('output')->andReturn($output);
        $log->shouldReceive('getAttribute')->once()->with('status')->andReturn($status);
        $log->shouldReceive('getAttribute')->atLeast()->once()->with('started_at')->andReturn($started_at);
        $log->shouldReceive('getAttribute')->atLeast()->once()->with('finished_at')->andReturn($finished_at);
        $log->shouldReceive('runtime')->once()->andReturn($runtime);

        return $log;
    }
}
