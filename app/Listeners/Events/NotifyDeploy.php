<?php

namespace REBELinBLUE\Deployer\Listeners\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use REBELinBLUE\Deployer\Events\DeployFinished;
use REBELinBLUE\Deployer\Jobs\MailDeployNotification;
use REBELinBLUE\Deployer\Jobs\RequestProjectCheckUrl;
use REBELinBLUE\Deployer\Jobs\SlackNotify;

/**
 * When a deploy finished, notify the followed user.
 */
class NotifyDeploy extends Event implements ShouldQueue
{
    use InteractsWithQueue, DispatchesJobs;

    /**
     * NotifyDeploy constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param DeployFinished $event
     * @dispatches SlackNotify
     * @dispatches MailDeployNotification
     * @dispatches RequestProjectCheckUrl
     */
    public function handle(DeployFinished $event)
    {
        $project    = $event->deployment->project;
        $deployment = $event->deployment;

        if ($deployment->isAborted()) {
            return;
        }

        // Send slack notifications
        foreach ($project->notifications as $notification) {
            if ($notification->failure_only === true && $deployment->isSuccessful()) {
                continue;
            }

            $this->dispatch(new SlackNotify($notification, $deployment->notificationPayload()));
        }

        // Send email notification
        $this->dispatch(new MailDeployNotification($project, $deployment));

        // Trigger to check the project urls
        $this->dispatch(new RequestProjectCheckUrl($project->checkUrls));
    }
}
