<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\Queue;

/**
 * Generic Job class.
 */
abstract class Job
{
    /*
    |--------------------------------------------------------------------------
    | Queueable Jobs
    |--------------------------------------------------------------------------
    |
    | This job base class provides a central location to place any logic that
    | is shared across all of your jobs. The trait included with the class
    | provides access to the "queueOn" and "delay" queue helper methods.
    |
    */

    use Queueable;

    /**
     * Overwrite the queue method to push to a different queue.
     *
     * @param  Queue $queue
     * @param  Job   $command
     * @return void
     */
    public function queue(Queue $queue, $command)
    {
        $queue->pushOn('low', $command);
    }
}
