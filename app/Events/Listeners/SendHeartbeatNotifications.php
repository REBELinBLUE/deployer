<?php

namespace REBELinBLUE\Deployer\Events\Listeners;

use Illuminate\Contracts\Translation\Translator;
use REBELinBLUE\Deployer\Events\HeartbeatChanged;
use REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatMissing;
use REBELinBLUE\Deployer\Notifications\Configurable\HeartbeatRecovered;

/**
 * Event handler class for heartbeat notifications.
 **/
class SendHeartbeatNotifications
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

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
            $channel->notify(new $notification($heartbeat, $this->translator));
        }
    }
}
