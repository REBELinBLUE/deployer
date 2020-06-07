<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

/**
 * Event which fires when the server status has changed.
 */
class ModelTrashed implements ShouldBroadcast
{
    use SerializesModels;

    /**
     * @var Model
     */
    public $model;

    /**
     * @var string
     */
    private $channel;

    /**
     * ModelTrashed constructor.
     *
     * @param Model  $model
     * @param string $channel
     */
    public function __construct(Model $model, string $channel)
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
    public function broadcastOn(): array
    {
        return [$this->channel];
    }
}
