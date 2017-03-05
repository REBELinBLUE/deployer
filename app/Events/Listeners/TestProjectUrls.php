<?php

namespace REBELinBLUE\Deployer\Events\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Events\DeploymentFinished;
use REBELinBLUE\Deployer\Jobs\RequestProjectCheckUrl;

/**
 * When a deploy finished, notify the followed user.
 */
class TestProjectUrls implements ShouldQueue
{
    use DispatchesJobs;

    /**
     * Handle the event.
     *
     * @param DeploymentFinished $event
     */
    public function handle(DeploymentFinished $event)
    {
        $project    = $event->deployment->project;
        $deployment = $event->deployment;

        if ($deployment->isAborted()) {
            return;
        }

        // Trigger to check the project urls
        $this->dispatch(new RequestProjectCheckUrl($project->checkUrls));
    }
}
