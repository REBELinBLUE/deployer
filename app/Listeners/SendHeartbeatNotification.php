<?php

namespace REBELinBLUE\Deployer\Listeners;

use REBELinBLUE\Deployer\Events\HeartbeatChanged;
use REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatMissing;
use REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatRecovered;

/**
 * Event handler class for heartbeat notifications.
 **/
class SendHeartbeatNotification
{
    /**
     * Handle the event.
     *
     * @param HeartbeatChanged $event
     */
    public function handle(HeartbeatChanged $event)
    {
        $heartbeat = $event->heartbeat;

        $notification = HeartbeatRecovered::class;
        $event        = 'heartbeat_recovered';
        if (!$heartbeat->isHealthy()) {
            $notification = HeartbeatMissing::class;
            $event        = 'heartbeat_missing';

            if ($heartbeat->missed > 1) {
                $event = 'heartbeat_still_missing';
            }
        }

        foreach ($heartbeat->project->channels->where('on_' . $event, true) as $channel) {
            $channel->notify(new $notification($heartbeat));
        }
    }
}
