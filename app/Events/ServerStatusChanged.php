<?php

namespace App\Events;

use App\Server;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ServerStatusChanged extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $server_id;
    public $status;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Server $server)
    {
        $this->server_id = $server->id;
        $this->status    = $server->status;
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
