<?php

namespace App\Events;

use App\Server;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Event which fires when the server status has changed.
 */
class ModelCreated extends Event implements ShouldBroadcast
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
        $this->model = $model;
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
