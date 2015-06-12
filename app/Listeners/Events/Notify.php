<?php

namespace App\Listeners\Events;

use App\Jobs\Notify as SlackNotify;
use App\Jobs\MailDeployNotification;
use App\Jobs\RequestProjectCheckUrl;
use App\Events\DeployFinished;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * When a deploy finished, notify the followed user.
 */
class Notify implements ShouldQueue
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
     * @param  DeployFinished  $event
     * @return void
     */
    public function handle(DeployFinished $event)
    {
        $project = $event->project;
        $deployment = $event->deployment;

        foreach ($project->notifications as $notification) {
            $this->dispatch(new SlackNotify($notification, $deployment->notificationPayload()));
        }

        //Send email notification
        $this->dispatch(new MailDeployNotification($project, $deployment));

        //Trigger to check the project urls
        $this->dispatch(new RequestProjectCheckUrl($project->checkUrls));
    }
}
