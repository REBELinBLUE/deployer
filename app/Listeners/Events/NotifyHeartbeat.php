<?php

namespace REBELinBLUE\Deployer\Listeners\Events;

use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Contracts\Events\HasSlackPayloadInterface;
use REBELinBLUE\Deployer\Jobs\SlackNotify;

/**
 * Event handler class for heartbeat recovery.
 **/
class NotifyHeartbeat extends Event
{
    use DispatchesJobs;

    /**
     * Create the event listener.
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
     * @param  Event $event
     * @return void
     */
    public function handle(HasSlackPayloadInterface $event)
    {
        foreach ($event->heartbeat->project->notifications as $notification) {
            $this->dispatch(new SlackNotify($notification, $event->notificationPayload()));
        }
    }
}
