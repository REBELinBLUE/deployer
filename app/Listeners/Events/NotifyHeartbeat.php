<?php

namespace REBELinBLUE\Deployer\Listeners\Events;

use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Events\Contracts\HasSlackPayload;
use REBELinBLUE\Deployer\Events\Event;
use REBELinBLUE\Deployer\Jobs\SlackNotify;

/**
 * Event handler class for heartbeat recovery.
 **/
class NotifyHeartbeat
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
    public function handle(HasSlackPayload $event)
    {
        $heartbeat = $event->heartbeat;

        foreach ($heartbeat->project->notifications as $notification) {
            $this->dispatch(new SlackNotify($notification, $event->notificationPayload()));
        }
    }
}
