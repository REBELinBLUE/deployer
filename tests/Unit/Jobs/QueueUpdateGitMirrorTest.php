<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Mockery as m;
use REBELinBLUE\Deployer\Jobs\QueueUpdateGitMirror;
use REBELinBLUE\Deployer\Jobs\UpdateGitMirror;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\QueueUpdateGitMirror
 */
class QueueUpdateGitMirrorTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle()
    {
        $project = m::mock(Project::class);

        $this->expectsJobs(UpdateGitMirror::class);

        $job = new QueueUpdateGitMirror($project);
        $job->handle();
    }

    /**
     * @covers ::__construct
     */
    public function testItHasUnlimitedTimeout()
    {
        $project = m::mock(Project::class);

        $job = new QueueUpdateGitMirror($project);

        $this->assertSame(0, $job->timeout);
    }
}
