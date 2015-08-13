<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Events\Event;
use REBELinBLUE\Deployer\Project;

/**
 * Deploy finished event.
 */
class DeployFinished extends Event
{
    use SerializesModels;

    public $project;
    public $deployment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Project $project, Deployment $deployment)
    {
        $this->project    = $project;
        $this->deployment = $deployment;
    }
}
