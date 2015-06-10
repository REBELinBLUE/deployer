<?php namespace App\Listeners\Events;

abstract class Event
{
    /**
     * Overwrite the queue method to push to a different queue
     * 
     * @param Queue $queue
     * @param string $name
     * @param mixed $arguments
     * @return void
     */
    public function queue($queue, $name, $arguments)
    {
        //$queue->pushOn('low', $arguments);
    }
}