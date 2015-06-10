<?php

namespace App\Events;

use App\Deployment;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeploymentStatusChanged extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $deployment_id;
    public $status;
    public $project;
    public $branch;
    public $started;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Deployment $deployment)
    {
        $this->deployment_id = $deployment->id;
        $this->status        = $deployment->status;
        $this->project       = $deployment->project->name;
        $this->branch        = $deployment->branch;
        $this->started       = $deployment->started_at;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['deployment-status'];
    }
}
