<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Events\Event;
use REBELinBLUE\Deployer\ServerLog;

/**
 * Event which fires when the server log content has changed.
 */
class ServerOutputChanged extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $log_id;
    public $output;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ServerLog $log)
    {
        $this->log_id = $log->id;
        $this->output = $log->output;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['serverlog-' . $this->log_id];
    }
}
