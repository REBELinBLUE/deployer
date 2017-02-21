<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;
use REBELinBLUE\Deployer\View\Presenters\ServerLogPresenter;
use Robbo\Presenter\PresentableInterface;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\ServerLog
 */
class ServerLogTest extends TestCase
{
    use TestsModel;

    /**
     * @covers ::__construct
     */
    public function testIsPresentable()
    {
        $log = new ServerLog();

        $this->assertInstanceOf(PresentableInterface::class, $log);
    }

    /**
     * @covers ::getPresenter
     */
    public function testGetPresenter()
    {
        $log       = new ServerLog();
        $presenter = $log->getPresenter();

        $this->assertInstanceOf(ServerLogPresenter::class, $presenter);
        $this->assertSame($log, $presenter->getObject());
    }

    /**
     * @covers ::runtime
     */
    public function testGetRuntime()
    {
        $log = new ServerLog();

        $log->status      = ServerLog::COMPLETED;
        $log->started_at  = Carbon::create(2017, 1, 1, 12, 15, 35, 'UTC');
        $log->finished_at = Carbon::create(2017, 1, 1, 12, 15, 47, 'UTC');

        $this->assertSame(12, $log->runtime());
    }

    /**
     * @covers ::runtime
     */
    public function testGetRuntimeWhenUnfinished()
    {
        $log = new ServerLog();

        $this->assertFalse($log->runtime());
    }

    /**
     * @covers ::server
     */
    public function testServer()
    {
        $log    = new ServerLog();
        $actual = $log->server();

        $this->assertInstanceOf(BelongsTo::class, $actual);
        $this->assertBelongsTo('server', ServerLog::class);
    }
}
