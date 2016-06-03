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

    public $id;
    public $output;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ServerLog $log)
    {
        $this->id = $log->id;
        $this->output = ((is_null($log->output) || !strlen($log->output)) ? null : $log->output);
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['serverlog-' . $this->id];
    }
}
