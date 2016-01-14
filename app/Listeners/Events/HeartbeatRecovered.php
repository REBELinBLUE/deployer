<?php

namespace REBELinBLUE\Deployer\Listeners\Events;

use REBELinBLUE\Deployer\Events\HeartbeatRecovered;
use Illuminate\Foundation\Bus\DispatchesJobs;

class HeartbeatRecovered
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
     * @param  HeartbeatRecovered  $event
     * @return void
     */
    public function handle(HeartbeatRecovered $event)
    {
        $heartbeat = $event->heartbeat;

        foreach ($heartbeat->project->notifications as $notification) {
            $this->dispatch(new Notify(
                $notification,
                $heartbeat->notificationRecoveredPayload()
            ));
        }
    }
}
