<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events\Listeners;

use Illuminate\Support\Collection;
use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Events\DeploymentFinished;
use REBELinBLUE\Deployer\Events\Listeners\TestProjectUrls;
use REBELinBLUE\Deployer\Jobs\RequestProjectCheckUrl;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\Listeners\TestProjectUrls
 */
class TestProjectUrlsTest extends TestCase
{
    /**
     * @covers ::handle
     */
    public function testHandleDispatchesJob()
    {
        $deployment = $this->mockDeployment();

        $this->expectsJobs(RequestProjectCheckUrl::class);

        $listener = new TestProjectUrls();
        $listener->handle(new DeploymentFinished($deployment));
    }

    /**
     * @covers ::handle
     */
    public function testHandleDoesNotDispatchWhenAborted()
    {
        $deployment = $this->mockDeployment(true);

        $this->doesntExpectJobs(RequestProjectCheckUrl::class);

        $listener = new TestProjectUrls();
        $listener->handle(new DeploymentFinished($deployment));
    }

    private function mockDeployment($aborted = false)
    {
        $project = m::mock(Project::class);

        if (!$aborted) {
            $project->shouldReceive('getAttribute')->atLeast()->once()->with('checkUrls')->andReturn(new Collection());
        }

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->atLeast()->once()->with('project')->andReturn($project);
        $deployment->shouldReceive('isAborted')->andReturn($aborted);

        return $deployment;
    }
}
