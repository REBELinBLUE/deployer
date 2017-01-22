<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

/**
 * Event which fires when the server status has changed.
 */
class ModelTrashed implements ShouldBroadcast
{
    use SerializesModels;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * @var string
     */
    private $channel;

    /**
     * ModelTrashed constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $channel
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
