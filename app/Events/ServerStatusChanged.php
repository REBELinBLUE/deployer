<?php

namespace App\Events;

use App\Server;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ServerStatusChanged extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $server;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['server-status'];
    }
}
