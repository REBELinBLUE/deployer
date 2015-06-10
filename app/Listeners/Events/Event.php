<?php

namespace App\Listeners\Events;

/**
 * Generic Event class
 */
abstract class Event
{
    /**
     * Overwrite the queue method to push to a different queue
     *
     * @param Queue $queue
     * @param string $name
     * @param mixed $arguments
     * @return void
     * TODO: figure out how to get the events on another queue, this seems weird
     */
    // public function queue($queue, $name, $arguments)
    // {
    //     //$queue->pushOn('low', $arguments);
    // }
}
