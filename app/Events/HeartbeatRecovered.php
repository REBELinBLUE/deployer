<?php

namespace REBELinBLUE\Deployer\Events;

use REBELinBLUE\Deployer\Events\Event;
use Illuminate\Queue\SerializesModels;

class HeartbeatRecovered extends Event
{
    use SerializesModels;

    public $heartbeat;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Heartbeat $heartbeat)
    {
        $this->heartbeat = $heartbeat;
    }
}
