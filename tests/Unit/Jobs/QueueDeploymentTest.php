<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\DeployProject;
use REBELinBLUE\Deployer\Jobs\QueueDeployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\DeployStepRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerLogRepositoryInterface;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\QueueDeployment
 */
class QueueDeploymentTest extends TestCase
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var DeployStepRepositoryInterface
     */
    private $repository;

    /**
     * @var ServerLogRepositoryInterface
     */
    private $log;

    public function setUp()
    {
        parent::setUp();

        $timestamp = Carbon::create(2017, 1, 15, 12, 35, 00, 'UTC');

        Carbon::setTestNow($timestamp);

        $deployment_id = 3123;
        $project_id    = 1543;

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('id')->andReturn($project_id);
        $project->shouldReceive('getAttribute')->once()->with('commands')->andReturn([]);
        $project->shouldReceive('setAttribute')->once()->with('status', Project::PENDING);
        $project->shouldReceive('save')->once();

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('setAttribute')->once()->with('status', Deployment::PENDING);
        $deployment->shouldReceive('getAttribute')->twice()->with('project')->andReturn($project);
        $deployment->shouldReceive('freshTimestamp')->once()->andReturn($timestamp);
        $deployment->shouldReceive('setAttribute')->once()->with('started_at', $timestamp);
        $deployment->shouldReceive('setAttribute')->once()->with('project_id', $project_id);
        $deployment->shouldReceive('setAttribute')->once()->with('is_webhook', true);
        $deployment->shouldReceive('getAttribute')->once()->with('committer')->andReturnNull();
        $deployment->shouldReceive('setAttribute')->once()->with('committer', Deployment::LOADING);
        $deployment->shouldReceive('getAttribute')->once()->with('commit')->andReturnNull();
        $deployment->shouldReceive('setAttribute')->once()->with('commit', Deployment::LOADING);
        $deployment->shouldReceive('save')->once();

        // This is happening in deployproject, can we do without this in this test?
        $deployment->shouldReceive('getAttribute')->once()->with('id')->andReturn($deployment_id);

        $repository = m::mock(DeployStepRepositoryInterface::class);
        $log        = m::mock(ServerLogRepositoryInterface::class);

        Auth::shouldReceive('check')->andReturn(false);

        $this->project    = $project;
        $this->deployment = $deployment;
        $this->repository = $repository;
        $this->log        = $log;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::setDeploymentStatus
     * @covers ::buildCommandList
     */
    public function testHandleWorksWithNoAdditionalCommands()
    {
        $this->markTestIncomplete('Still being worked on');
        $this->expectsJobs(DeployProject::class);

        $job = new QueueDeployment($this->project, $this->deployment);
        $job->handle($this->repository, $this->log);
    }
}
