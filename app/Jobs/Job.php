<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\Queue;

/**
 * Generic Job class.
 */
abstract class Job
{
    use Queueable;

    /**
     * Overwrite the queue method to push to a different queue.
     *
     * @param Queue $queue
     * @param Job   $command
     */
    public function queue(Queue $queue, $command)
    {
        $queue->pushOn('deployer-low', $command);
    }
}
