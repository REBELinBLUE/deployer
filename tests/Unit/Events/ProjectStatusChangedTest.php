<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Mockery as m;
use REBELinBLUE\Deployer\Events\ProjectStatusChanged;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\ProjectStatusChanged
 */
class ProjectStatusChangedTest extends TestCase
{
    /**
     * @covers ::broadcastOn
     */
    public function testBroadcastOn()
    {
        $event = new ProjectStatusChanged($this->mockProject());
        $this->assertSame(['project-status'], $event->broadcastOn());
    }

    /**
     * @covers ::__construct
     */
    public function testIsBroadcastable()
    {
        $event = new ProjectStatusChanged($this->mockProject());
        $this->assertInstanceOf(ShouldBroadcast::class, $event);
    }

    private function mockProject()
    {
        return m::mock(Project::class);
    }
}
