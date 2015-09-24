<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Events\Event;
use REBELinBLUE\Deployer\ServerLog;

/**
 * Event which fires when the server log status has changed.
 */
class ServerLogChanged extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $log_id;
    public $output;
    public $runtime;
    public $status;
    public $started_at;
    public $finished_at;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ServerLog $log)
    {
        $this->status      = $log->status;
        $this->started_at  = $log->started_at ? $log->started_at->toDateTimeString() : null;
        $this->finished_at = $log->finished_at ? $log->finished_at->toDateTimeString() : null;
        $this->log_id      = $log->id;
        $this->output      = ((is_null($log->output) || !strlen($log->output)) ? null : '');
        $this->runtime     = ($log->runtime() === false ? null : $log->getPresenter()->readable_runtime);
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['serverlog'];
    }
}
