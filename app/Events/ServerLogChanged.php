<?php

namespace App\Events;

use App\ServerLog;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ServerLogChanged extends Event
{
    use SerializesModels;

    public $log;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ServerLog $log)
    {
        $this->log = $log;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['serverlog-status'];
    }
}
