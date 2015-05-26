<?php namespace App\Events;

use App\Deployment;
use App\Project;

use App\Events\Event;

use Illuminate\Queue\SerializesModels;

/**
 * Deploy finished event
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
        $this->project = $project;
        $this->deployment = $deployment;
    }
}
