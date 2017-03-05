<?php

namespace REBELinBLUE\Deployer\Events\Observers;

use Illuminate\Contracts\Events\Dispatcher;
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
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Called when the model is updated.
     *
     * @param ServerLog $log
     */
    public function updated(ServerLog $log)
    {
        $outputChanged = $log->isDirty('output');

        $this->dispatcher->dispatch(new ServerLogChanged($log));

        if ($outputChanged) {
            $this->dispatcher->dispatch(new ServerOutputChanged($log));
        }
    }
}
