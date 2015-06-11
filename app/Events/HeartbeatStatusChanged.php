<?php

namespace App\Events;

use App\Heartbeat;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Event which fires when a project heartbeat status has changed.
 */
class HeartbeatStatusChanged extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $heartbeat_id;
    public $status;
    public $last_activity;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Heartbeat $heartbeat)
    {
        $this->heartbeat_id = $heartbeat->id;
        $this->status = $heartbeat->status;
        $this->last_activity = $heartbeat->last_activity; // FIXME: Change so it only sends the actual date not timezone and timezone_type
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['heartbeat-status'];
    }
}
