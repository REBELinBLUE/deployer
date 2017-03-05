<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events\Observers;

use McCool\LaravelAutoPresenter\AutoPresenter;
use Mockery as m;
use REBELinBLUE\Deployer\Events\Observers\ServerLogObserver;
use REBELinBLUE\Deployer\Events\ServerLogChanged;
use REBELinBLUE\Deployer\Events\ServerOutputChanged;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\Observers\ServerLogObserver
 */
class ServerLogObserverTest extends TestCase
{
    private $dispatcher;
    private $presenter;

    public function setUp()
    {
        parent::setUp();

        $this->withoutEvents();

        $this->dispatcher = $this->app->make('events');
        $this->presenter  = m::mock(AutoPresenter::class);
        $this->presenter->shouldReceive('decorate');
    }

    /**
     * @covers ::__construct
     * @covers ::updated
     */
    public function testUpdatedNotFireOutputChangedEventWhenOutputNotChanged()
    {
        /** @var ServerLog $log */
        $log = factory(ServerLog::class)->make([
            'server_id'      => 1,
            'deploy_step_id' => 1,
            'status'         => ServerLog::RUNNING,
            'output'         => 'Lorem ipsum',
        ])->syncOriginal();

        $this->expectsEvents(ServerLogChanged::class);
        $this->doesntExpectEvents(ServerOutputChanged::class);

        $observer = new ServerLogObserver($this->dispatcher, $this->presenter);
        $observer->updated($log);
    }

    /**
     * @covers ::__construct
     * @covers ::updated
     */
    public function testUpdatedFiresOutputChangedEventWhenOutputChanged()
    {
        /** @var ServerLog $log */
        $log = factory(ServerLog::class)->make([
            'server_id'      => 1,
            'deploy_step_id' => 1,
            'status'         => ServerLog::RUNNING,
            'output'         => 'Lorem ipsum',
        ])->syncOriginal();

        $this->expectsEvents([ServerLogChanged::class, ServerOutputChanged::class]);

        $log->status = ServerLog::COMPLETED;
        $log->output = 'lorem ipsum';

        $observer = new ServerLogObserver($this->dispatcher, $this->presenter);
        $observer->updated($log);
    }
}
