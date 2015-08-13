<?php

namespace REBELinBLUE\Deployer\Events;

use REBELinBLUE\Deployer\Events\Event;
use REBELinBLUE\Deployer\Project;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

/**
 * Event which fires when the project status has changed.
 */
class ProjectStatusChanged extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $project;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['project-status'];
    }
}
