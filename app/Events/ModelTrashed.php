<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Events\Event;

/**
 * Event which fires when the server status has changed.
 */
class ModelTrashed extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $model;
    private $channel;

    /**
     * Create a new event instance.
     *
     * @param string $channel
     * @return void
     */
    public function __construct($model, $channel)
    {
        $this->model = [
            'id'         => $model->id,
            'project_id' => $model->project_id,
        ];

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
