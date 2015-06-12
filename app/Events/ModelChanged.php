<?php

namespace App\Events;

use App\Events\Event;
use App\Server;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

/**
 * Event which fires when the server status has changed.
 */
class ModelChanged extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $model;
    private $channel;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($model, $channel)
    {
        $this->model   = $model;
        $this->channel = $channel;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [$this->channel];
    }
}
