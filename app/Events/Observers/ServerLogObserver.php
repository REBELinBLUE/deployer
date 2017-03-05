<?php

namespace REBELinBLUE\Deployer\Events\Observers;

use Illuminate\Contracts\Events\Dispatcher;
use McCool\LaravelAutoPresenter\AutoPresenter;
use REBELinBLUE\Deployer\Events\ServerLogChanged;
use REBELinBLUE\Deployer\Events\ServerOutputChanged;
use REBELinBLUE\Deployer\ServerLog;

/**
 * Event observer for ServerLog model.
 */
class ServerLogObserver
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var AutoPresenter
     */
    private $presenter;

    /**
     * @param Dispatcher    $dispatcher
     * @param AutoPresenter $presenter
     */
    public function __construct(Dispatcher $dispatcher, AutoPresenter $presenter)
    {
        $this->dispatcher = $dispatcher;
        $this->presenter  = $presenter;
    }

    /**
     * Called when the model is updated.
     *
     * @param ServerLog $log
     */
    public function updated(ServerLog $log)
    {
        $outputChanged = $log->isDirty('output');

        $this->dispatcher->dispatch(new ServerLogChanged($log, $this->presenter));

        if ($outputChanged) {
            $this->dispatcher->dispatch(new ServerOutputChanged($log));
        }
    }
}
