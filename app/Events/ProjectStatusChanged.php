<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Project;

/**
 * Event which fires when the project status has changed.
 */
class ProjectStatusChanged implements ShouldBroadcast
{
    use SerializesModels;

    /**
     * @var Project
     */
    public $project;

    /**
     * ProjectStatusChanged constructor.
     *
     * @param Project $project
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
