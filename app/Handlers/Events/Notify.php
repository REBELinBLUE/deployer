<?php namespace App\Handlers\Events;

use Queue;
use App\Commands\Notify as SlackNotify;
use App\Commands\MailDeployNotification;
use App\Events\DeployFinished;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Foundation\Bus\DispatchesCommands;

/**
 * When a deploy finished, notify the followed user.
 */
class Notify implements ShouldBeQueued
{
    use InteractsWithQueue;
    use DispatchesCommands;
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
            Queue::pushOn('notify', new SlackNotify($notification, $deployment->notificationPayload()));
        }

        //Send email notification
        $this->dispatch(new MailDeployNotification($project, $deployment));
    }
}
