<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Illuminate\Queue\Queue;
use Mockery as m;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\stubs\Job;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\Job
 */
class JobTest extends TestCase
{
    /**
     * @covers ::queue
     */
    public function testQueue()
    {
        $job = new Job();

        $queue = m::mock(Queue::class);
        $queue->shouldReceive('pushOn')->once()->with('deployer-low', $job);

        $job->queue($queue, $job);
    }
}
