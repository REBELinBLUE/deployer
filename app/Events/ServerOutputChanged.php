<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\ServerLog;

/**
 * Event which fires when the server log content has changed.
 */
class ServerOutputChanged implements ShouldBroadcast
{
    use SerializesModels;

    /**
     * @var int
     */
    public $log_id;

    /**
     * @var string
     */
    public $output;

    /**
     * ServerOutputChanged constructor.
     *
     * @param ServerLog $log
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
