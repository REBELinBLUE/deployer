<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Collection;
use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\DeployProject;
use REBELinBLUE\Deployer\Jobs\QueueDeployment;
use REBELinBLUE\Deployer\Jobs\QueueDeployment\GroupedCommandListTransformer;
use REBELinBLUE\Deployer\Jobs\QueueDeployment\StepsBuilder;
use REBELinBLUE\Deployer\Project;
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
     * @var GroupedCommandListTransformer
     */
    private $builder;

    /**
     * @var StepsBuilder
     */
    private $steps;

    /**
     * @var Guard
     */
    private $auth;

    /**
     * @todo Improve this, not testing groups, or optional
     */
    public function setUp()
    {
        parent::setUp();

        $timestamp = Carbon::create(2017, 1, 15, 12, 35, 00, 'UTC');

        Carbon::setTestNow($timestamp);

        $deployment_id = 3123;
        $project_id    = 1543;
        $release_id    = 20170101165625;
        $grouped       = new Collection();

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->with('id')->andReturn($project_id);
        $project->shouldReceive('setAttribute')->once()->with('status', Project::PENDING);
        $project->shouldReceive('save')->once();

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('id')->andReturn($deployment_id);
        $deployment->shouldReceive('setAttribute')->once()->with('status', Deployment::PENDING);
        $deployment->shouldReceive('getAttribute')->with('project')->andReturn($project);
        $deployment->shouldReceive('freshTimestamp')->andReturn($timestamp);
        $deployment->shouldReceive('setAttribute')->once()->with('started_at', $timestamp);
        $deployment->shouldReceive('setAttribute')->once()->with('project_id', $project_id);
        $deployment->shouldReceive('getAttribute')->with('project_id')->andReturn($project_id);
        $deployment->shouldReceive('getAttribute')->with('release_id')->andReturn($release_id);
        $deployment->shouldReceive('save')->once();

        $builder = m::mock(GroupedCommandListTransformer::class);
        $builder->shouldReceive('groupCommandsByDeployStep')->once()->with($project)->andReturn($grouped);

        $steps = m::mock(StepsBuilder::class);
        $steps->shouldReceive('build')->once()->with($grouped, $project, $deployment_id, []);

        $auth = m::mock(Guard::class);

        $this->project    = $project;
        $this->deployment = $deployment;
        $this->builder    = $builder;
        $this->steps      = $steps;
        $this->auth       = $auth;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::setDeploymentStatus
     */
    public function testHandleWithNoUser()
    {
        $this->expectsJobs(DeployProject::class);

        $this->auth->shouldReceive('check')->andReturn(false);

        $this->deployment->shouldReceive('getAttribute')->with('committer')->andReturnNull();
        $this->deployment->shouldReceive('setAttribute')->once()->with('committer', Deployment::LOADING);
        $this->deployment->shouldReceive('getAttribute')->with('commit')->andReturnNull();
        $this->deployment->shouldReceive('setAttribute')->once()->with('commit', Deployment::LOADING);

        $this->deployment->shouldReceive('setAttribute')->once()->with('is_webhook', true);
        $this->deployment->shouldNotReceive('setAttribute')->with('user_id', m::any());

        $job = new QueueDeployment($this->project, $this->deployment, []);
        $job->handle($this->builder, $this->steps, $this->auth);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::setDeploymentStatus
     */
    public function testHandleWithUser()
    {
        $expectedId = 6;

        $this->auth->shouldReceive('check')->andReturn(true);
        $this->auth->shouldReceive('id')->andReturn($expectedId);

        $this->expectsJobs(DeployProject::class);

        $this->deployment->shouldReceive('getAttribute')->with('committer')->andReturnNull();
        $this->deployment->shouldReceive('setAttribute')->once()->with('committer', Deployment::LOADING);
        $this->deployment->shouldReceive('getAttribute')->with('commit')->andReturnNull();
        $this->deployment->shouldReceive('setAttribute')->once()->with('commit', Deployment::LOADING);

        $this->deployment->shouldNotReceive('setAttribute')->with('is_webhook', m::type('boolean'));
        $this->deployment->shouldReceive('setAttribute')->once()->with('user_id', $expectedId);

        $job = new QueueDeployment($this->project, $this->deployment, []);
        $job->handle($this->builder, $this->steps, $this->auth);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::setDeploymentStatus
     */
    public function testHandleExistingCommitDetailsForWebhook()
    {
        $this->expectsJobs(DeployProject::class);

        $committer = 'bob smith';
        $commit    = 'a-git-commit-hash';

        $this->auth->shouldReceive('check')->andReturn(false);

        $this->deployment->shouldReceive('getAttribute')->with('committer')->andReturn($committer);
        $this->deployment->shouldReceive('setAttribute')->once()->with('committer', $committer);
        $this->deployment->shouldReceive('getAttribute')->with('commit')->andReturn($commit);
        $this->deployment->shouldReceive('setAttribute')->once()->with('commit', $commit);

        $this->deployment->shouldReceive('setAttribute')->once()->with('is_webhook', true);
        $this->deployment->shouldNotReceive('setAttribute')->with('user_id', m::any());

        $job = new QueueDeployment($this->project, $this->deployment, []);
        $job->handle($this->builder, $this->steps, $this->auth);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::setDeploymentStatus
     */
    public function testHandleExistingCommitDetailsWhenUserAuthenticated()
    {
        $this->expectsJobs(DeployProject::class);

        $expectedId = 6;

        $this->auth->shouldReceive('check')->andReturn(true);
        $this->auth->shouldReceive('id')->andReturn($expectedId);

        $committer = 'bob smith';
        $commit    = 'a-git-commit-hash';

        $this->deployment->shouldReceive('getAttribute')->with('committer')->andReturn($committer);
        $this->deployment->shouldReceive('setAttribute')->once()->with('committer', $committer);
        $this->deployment->shouldReceive('getAttribute')->with('commit')->andReturn($commit);
        $this->deployment->shouldReceive('setAttribute')->once()->with('commit', $commit);

        $this->deployment->shouldNotReceive('setAttribute')->with('is_webhook', m::type('boolean'));
        $this->deployment->shouldReceive('setAttribute')->once()->with('user_id', $expectedId);

        $job = new QueueDeployment($this->project, $this->deployment, []);
        $job->handle($this->builder, $this->steps, $this->auth);
    }
}
