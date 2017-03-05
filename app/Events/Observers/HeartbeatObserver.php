<?php

namespace REBELinBLUE\Deployer\Events\Observers;

use REBELinBLUE\Deployer\Heartbeat;

/**
 * Event observer for Heartbeat model.
 */
class HeartbeatObserver
{
    /**
     * Called when the model is being created.
     *
     * @param Heartbeat $heartbeat
     */
    public function creating(Heartbeat $heartbeat)
    {
        if (empty($heartbeat->hash)) {
            $heartbeat->generateHash();
        }
    }
}
