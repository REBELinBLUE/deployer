<?php

namespace REBELinBLUE\Deployer\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use REBELinBLUE\Deployer\Events\DeploymentFinished;
use REBELinBLUE\Deployer\Notifications\Configurable\DeploymentFailed;
use REBELinBLUE\Deployer\Notifications\Configurable\DeploymentSucceeded;

/**
 * When a deploy finished, notify the followed user.
 */
class SendDeploymentNotifications implements ShouldQueue
{
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

        $notification = DeploymentFailed::class;
        $event        = 'deployment_failure';
        if ($deployment->isSuccessful()) {
            $notification = DeploymentSucceeded::class;
            $event        = 'deployment_success';
        }

        foreach ($project->channels->where('on_' . $event, true) as $channel) {
            $channel->notify(new $notification($project, $deployment));
        }
    }
}
