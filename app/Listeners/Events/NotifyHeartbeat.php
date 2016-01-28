<?php

namespace REBELinBLUE\Deployer\Listeners\Events;

use REBELinBLUE\Deployer\Events\HeartbeatRecovered;
use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Events\Event;
use REBELinBLUE\Deployer\Jobs\Notify as SlackNotify;

/**
 * Event handler class for heartbeat recovery
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
     * @param  Event  $event
     * @return void
     */
    public function handle(Event $event)
    {
        $heartbeat = $event->heartbeat;

        if ($event instanceof HeartbeatRecovered) {
            $payload = $heartbeat->notificationRecoveredPayload();
        } else {
            $payload = $heartbeat->notificationMissingPayload();
        }

        foreach ($heartbeat->project->notifications as $notification) {
            $this->dispatch(new SlackNotify(
                $notification,
                $payload
            ));
        }
    }
}
