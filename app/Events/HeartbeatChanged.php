<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Heartbeat;

/**
 * Event class which is thrown when the heartbeat changes.
 **/
abstract class HeartbeatChanged
{
    use SerializesModels;

    /**
     * @var Heartbeat
     */
    public $heartbeat;

    /**
     * HeartbeatChanged constructor.
     *
     * @param Heartbeat $heartbeat
     */
    public function __construct(Heartbeat $heartbeat)
    {
        $this->heartbeat = $heartbeat;
    }
}
