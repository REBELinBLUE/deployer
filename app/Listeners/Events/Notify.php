<?php

namespace App\Listeners\Events;

use App\Events\DeployFinished;
use App\Jobs\MailDeployNotification;
use App\Jobs\Notify as SlackNotify;
use App\Jobs\RequestProjectCheckUrl;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;

/**
 * When a deploy finished, notify the followed user.
 */
class Notify extends Event implements ShouldQueue
{
    use InteractsWithQueue, DispatchesJobs;

    /**
     * Create the event handler.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DeployFinished $event
     * @return void
     */
    public function handle(DeployFinished $event)
    {
        $project    = $event->project;
        $deployment = $event->deployment;

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
