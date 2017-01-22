<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * An event to restart the socket server.
 */
class RestartSocketServer implements ShouldBroadcast
{
    /**
     * @var string
     */
    public $message;

    /**
     * RestartSocketServer constructor.
     */
    public function __construct()
    {
        $this->message = 'restart';
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['restart'];
    }
}
