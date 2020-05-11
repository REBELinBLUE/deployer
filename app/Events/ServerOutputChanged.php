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
     * ServerOutputChanged constructor.
     *
     * @param ServerLog $log
     */
    public function __construct(ServerLog $log)
    {
        $this->log_id = $log->id;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn(): array
    {
        return ['serverlog-' . $this->log_id];
    }
}
