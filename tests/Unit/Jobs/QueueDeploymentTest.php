<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Carbon\Carbon;
use Mockery as m;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\DeployProject;
use REBELinBLUE\Deployer\Jobs\QueueDeployment;
use REBELinBLUE\Deployer\Jobs\QueueDeployment\CommandCreator;
use REBELinBLUE\Deployer\Jobs\QueueDeployment\GroupedCommandListBuilder;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;

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
     * @var GroupedCommandListBuilder
     */
    private $builder;

    /**
     * @var CommandCreator
     */
    private $commands;

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
        $grouped       = [
            Command::DO_CLONE    => null,
            Command::DO_INSTALL  => null,
            Command::DO_ACTIVATE => null,
            Command::DO_PURGE    => null,
        ];

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('id')->andReturn($project_id);
        $project->shouldReceive('setAttribute')->once()->with('status', Project::PENDING);
        $project->shouldReceive('save')->once();

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('setAttribute')->once()->with('status', Deployment::PENDING);
        $deployment->shouldReceive('getAttribute')->twice()->with('project')->andReturn($project);
        $deployment->shouldReceive('freshTimestamp')->once()->andReturn($timestamp);
        $deployment->shouldReceive('setAttribute')->once()->with('started_at', $timestamp);
        $deployment->shouldReceive('setAttribute')->once()->with('project_id', $project_id);
        $deployment->shouldReceive('save')->once();

        // This is happening in deploy project, can we do without this in this test?
        $deployment->shouldReceive('getAttribute')->once()->with('id')->andReturn($deployment_id);

        $builder = m::mock(GroupedCommandListBuilder::class);
        $builder->shouldReceive('groupCommandsByStep')->once()->with($project)->andReturn($grouped);

        $commands = m::mock(CommandCreator::class);
        $commands->shouldReceive('build')->once()->with($grouped, $project, $deployment, []);

        $this->project    = $project;
        $this->deployment = $deployment;
        $this->builder    = $builder;
        $this->commands   = $commands;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::setDeploymentStatus
     */
    public function testHandleWithNoUser()
    {
        $this->expectsJobs(DeployProject::class);

        $this->deployment->shouldReceive('getAttribute')->once()->with('committer')->andReturnNull();
        $this->deployment->shouldReceive('setAttribute')->once()->with('committer', Deployment::LOADING);
        $this->deployment->shouldReceive('getAttribute')->once()->with('commit')->andReturnNull();
        $this->deployment->shouldReceive('setAttribute')->once()->with('commit', Deployment::LOADING);

        $this->deployment->shouldReceive('setAttribute')->once()->with('is_webhook', true);
        $this->deployment->shouldNotReceive('setAttribute')->with('user_id', m::any());

        $job = new QueueDeployment($this->project, $this->deployment, []);
        $job->handle($this->builder, $this->commands);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::setDeploymentStatus
     */
    public function testHandleWithUser()
    {
        $user     = new User(['name' => 'John']);
        $user->id = 6;

        $this->be($user);

        $this->expectsJobs(DeployProject::class);


        $this->deployment->shouldReceive('getAttribute')->once()->with('committer')->andReturnNull();
        $this->deployment->shouldReceive('setAttribute')->once()->with('committer', Deployment::LOADING);
        $this->deployment->shouldReceive('getAttribute')->once()->with('commit')->andReturnNull();
        $this->deployment->shouldReceive('setAttribute')->once()->with('commit', Deployment::LOADING);

        $this->deployment->shouldNotReceive('setAttribute')->with('is_webhook', m::type('boolean'));
        $this->deployment->shouldReceive('setAttribute')->once()->with('user_id', $user->id);

        $job = new QueueDeployment($this->project, $this->deployment, []);
        $job->handle($this->builder, $this->commands);
    }


    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::setDeploymentStatus
     */
    public function testHandleExistingCommitDetails()
    {
        $this->expectsJobs(DeployProject::class);

        $committer = 'bob smith';
        $commit = 'a-git-commit-hash';

        $this->deployment->shouldReceive('getAttribute')->once()->with('committer')->andReturn($committer);
        $this->deployment->shouldReceive('setAttribute')->once()->with('committer', $committer);
        $this->deployment->shouldReceive('getAttribute')->once()->with('commit')->andReturn($commit);
        $this->deployment->shouldReceive('setAttribute')->once()->with('commit', $commit);

        $this->deployment->shouldReceive('setAttribute')->with('is_webhook', true);

        $job = new QueueDeployment($this->project, $this->deployment, []);
        $job->handle($this->builder, $this->commands);
    }
}
