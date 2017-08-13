<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Carbon\Carbon;
use Illuminate\Queue\Queue;
use Illuminate\Support\Collection;
use Mockery as m;
use phpmock\mockery\PHPMockery as phpm;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Events\DeploymentFinished;
use REBELinBLUE\Deployer\Exceptions\CancelledDeploymentException;
use REBELinBLUE\Deployer\Exceptions\FailedDeploymentException;
use REBELinBLUE\Deployer\Jobs\DeployProject;
use REBELinBLUE\Deployer\Jobs\DeployProject\CleanupFailedDeployment;
use REBELinBLUE\Deployer\Jobs\DeployProject\ReleaseArchiver;
use REBELinBLUE\Deployer\Jobs\DeployProject\RunDeploymentStep;
use REBELinBLUE\Deployer\Jobs\DeployProject\UpdateRepositoryInfo;
use REBELinBLUE\Deployer\Jobs\UpdateGitMirror;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Tests\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\DeployProject
 * @FIXMNE: Needs refactoring
 */
class DeployProjectTest extends TestCase
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::cleanup
     */
    public function testHandle()
    {
        $this->setUpExpections();
        $this->expectsJobs(RunDeploymentStep::class);

        $steps = new Collection([new DeployStep()]);

        $this->deployment->shouldReceive('getAttribute')->with('steps')->andReturn($steps);

        $finished = Carbon::create(2017, 1, 5, 16, 42, 31, 'UTC');
        $this->project->shouldReceive('setAttribute')->with('last_run', $finished);
        $this->deployment->shouldReceive('freshTimestamp')->once()->andReturn($finished);
        $this->deployment->shouldReceive('setAttribute')->with('finished_at', $finished);

        $this->deployment->shouldReceive('getAttribute')->with('finished_at')->andReturn($finished);

        $job = new DeployProject($this->deployment);
        $job->handle($this->filesystem);
    }

    /**
     * @covers ::__construct
     */
    public function testItHasUnlimitedTimeout()
    {
        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute');

        $job = new DeployProject($deployment);

        $this->assertSame(0, $job->timeout);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::cleanup
     * @covers ::fail
     */
    public function testHandleDealsWithException()
    {
        $this->setUpExpections();

        // FIXME: This is a horrible way to test this
        $this->deployment->shouldReceive('getAttribute')->once()->with('steps')->andThrow(RuntimeException::class);

        $this->handleMostExceptions();

        $job = new DeployProject($this->deployment);
        $job->handle($this->filesystem);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::cleanup
     * @covers ::fail
     */
    public function testHandleDealsWithFailedDeploymentException()
    {
        $this->setUpExpections();

        // FIXME: This is a horrible way to test this
        $this->deployment->shouldReceive('getAttribute')
                         ->once()
                         ->with('steps')
                         ->andThrow(FailedDeploymentException::class);

        $this->handleMostExceptions();

        // FIXME: This also doesn't work correctly, activated is true
        $this->deployment->shouldReceive('setAttribute')->with('status', Deployment::COMPLETED_WITH_ERRORS);

        $this->project->shouldReceive('setAttribute')->with('status', Project::FINISHED);

        $job = new DeployProject($this->deployment);
        $job->handle($this->filesystem);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::cleanup
     * @covers ::fail
     */
    public function testHandleDealsWithCancelledDeploymentException()
    {
        $this->setUpExpections();

        $this->expectsJobs(CleanupFailedDeployment::class);

        // FIXME: This is a horrible way to test this
        $this->deployment->shouldReceive('getAttribute')
                         ->once()
                         ->with('steps')
                         ->andThrow(CancelledDeploymentException::class);

        $log = m::mock(ServerLog::class);
        $log->shouldReceive('getAttribute')->once()->with('status')->andReturn(ServerLog::PENDING);
        $log->shouldReceive('setAttribute')->once()->with('status', ServerLog::CANCELLED);
        $log->shouldReceive('save')->once();

        $step = m::mock(DeployStep::class);
        $step->shouldReceive('getAttribute')->with('servers')->andReturn(new Collection([$log]));
        $step->shouldReceive('getAttribute')->with('stage')->andReturn(Command::BEFORE_ACTIVATE);
        $steps = new Collection([$step]);

        $this->deployment->shouldReceive('setAttribute')->with('status', Deployment::FAILED);
        $this->deployment->shouldReceive('setAttribute')->with('status', Deployment::ABORTED);
        $this->project->shouldReceive('setAttribute')->with('status', Project::FAILED);
        $this->deployment->shouldReceive('getAttribute')->once()->with('steps')->andReturn($steps);

        $this->project->shouldReceive('setAttribute')->with('last_run', null);
        $this->deployment->shouldReceive('getAttribute')->with('finished_at')->andReturnNull();

        $job = new DeployProject($this->deployment);
        $job->handle($this->filesystem);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::cleanup
     * @covers ::fail
     */
    public function testHandleDealsWithFailedDeploymentExceptionWhenActivated()
    {
        $this->setUpExpections();

        $this->doesntExpectJobs(CleanupFailedDeployment::class);

        // FIXME: This is a horrible way to test this
        $this->deployment->shouldReceive('getAttribute')
            ->once()
            ->with('steps')
            ->andThrow(FailedDeploymentException::class);

        $log = m::mock(ServerLog::class);
        $log->shouldReceive('getAttribute')->once()->with('status')->andReturn(ServerLog::PENDING);
        $log->shouldReceive('setAttribute')->once()->with('status', ServerLog::CANCELLED);
        $log->shouldReceive('save')->once();

        $step = m::mock(DeployStep::class);
        $step->shouldReceive('getAttribute')->with('servers')->andReturn(new Collection([$log]));
        $step->shouldReceive('getAttribute')->with('stage')->andReturn(Command::AFTER_ACTIVATE);
        $steps = new Collection([$step]);

        $this->deployment->shouldReceive('setAttribute')->with('status', Deployment::FAILED);
        $this->deployment->shouldReceive('setAttribute')->with('status', Deployment::COMPLETED_WITH_ERRORS);

        $this->project->shouldReceive('setAttribute')->with('status', Project::FAILED);
        $this->project->shouldReceive('setAttribute')->with('status', Project::FINISHED);
        $this->deployment->shouldReceive('getAttribute')->once()->with('steps')->andReturn($steps);

        $finished = Carbon::create(2017, 1, 5, 16, 42, 31, 'UTC');
        $this->project->shouldReceive('setAttribute')->with('last_run', $finished);
        $this->deployment->shouldReceive('freshTimestamp')->once()->andReturn($finished);
        $this->deployment->shouldReceive('setAttribute')->with('finished_at', $finished);
        $this->deployment->shouldReceive('getAttribute')->with('finished_at')->andReturn($finished);

        $job = new DeployProject($this->deployment);
        $job->handle($this->filesystem);
    }

    /**
     * @covers ::__construct
     * @covers ::queue
     */
    public function testQueue()
    {
        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('project_id')->andReturn(1);
        $deployment->shouldReceive('getAttribute')->with('release_id')->andReturn(1);

        $job = new DeployProject($deployment);

        $queue = m::mock(Queue::class);
        $queue->shouldReceive('pushOn')->once()->with('deployer-high', $job);

        $job->queue($queue, $job);
    }

    private function setUpExpections()
    {
        $deployment_id = 10;
        $project_id    = 1;
        $key_file      = '/tmp/ssh.keyfile';
        $private_key   = 'a-git-key';
        $release_id    = 20170105163412;
        $archive       = storage_path('app/1_20170105163412.tar.gz');

        $started  = Carbon::create(2017, 1, 5, 16, 34, 12, 'UTC');

        $sleepTimes = 5;

        $project = m::mock(Project::class);
        $project->shouldReceive('setAttribute')->with('status', Project::DEPLOYING);
        $project->shouldReceive('getAttribute')->with('private_key')->andReturn($private_key);
        $project->shouldReceive('setAttribute')->with('status', Project::FINISHED);
        $project->shouldReceive('save')->twice();
        $project->shouldReceive('getAttribute')->times($sleepTimes)->with('is_mirroring')->andReturn(true);
        $project->shouldReceive('fresh')->andReturnSelf();
        $project->shouldReceive('getAttribute')->once()->with('is_mirroring')->andReturn(false);

        phpm::mock('REBELinBLUE\Deployer\Jobs', 'sleep')->times($sleepTimes);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('id')->andReturn($deployment_id);
        $deployment->shouldReceive('getAttribute')->with('project_id')->andReturn($project_id);
        $deployment->shouldReceive('getAttribute')->with('release_id')->andReturn($release_id);
        $deployment->shouldReceive('freshTimestamp')->once()->andReturn($started);
        $deployment->shouldReceive('setAttribute')->with('status', Deployment::DEPLOYING);
        $deployment->shouldReceive('setAttribute')->with('started_at', $started);
        $deployment->shouldReceive('getAttribute')->with('project')->andReturn($project);
        $deployment->shouldReceive('setAttribute')->with('status', Deployment::COMPLETED);
        $deployment->shouldReceive('getAttribute')->with('status')->andReturn(Deployment::COMPLETED);
        $deployment->shouldReceive('save')->twice();

        $filesystem = m::mock(Filesystem::class);
        $filesystem->shouldReceive('tempnam')->with(storage_path('app/tmp/'), 'key')->andReturn($key_file);
        $filesystem->shouldReceive('put')->with($key_file, $private_key);
        $filesystem->shouldReceive('chmod')->with($key_file, 0600);
        $filesystem->shouldReceive('exists')->with($archive)->andReturn(true);
        $filesystem->shouldReceive('delete')->with([$key_file, $archive]);

        $this->expectsJobs([
            UpdateGitMirror::class,
            UpdateRepositoryInfo::class,
            ReleaseArchiver::class,
        ]);

        $this->expectsEvents(DeploymentFinished::class);

        $this->project    = $project;
        $this->filesystem = $filesystem;
        $this->deployment = $deployment;
    }

    /**
     * @fixme test the cleanup
     */
    private function handleMostExceptions()
    {
        $this->deployment->shouldReceive('setAttribute')->with('status', Deployment::FAILED);
        $this->project->shouldReceive('setAttribute')->with('status', Project::FAILED);
        $this->deployment->shouldReceive('getAttribute')->once()->with('steps')->andReturn(new Collection());

        $finished = Carbon::create(2017, 1, 5, 16, 42, 31, 'UTC');
        $this->project->shouldReceive('setAttribute')->with('last_run', $finished);
        $this->deployment->shouldReceive('freshTimestamp')->once()->andReturn($finished);
        $this->deployment->shouldReceive('setAttribute')->with('finished_at', $finished);
        $this->deployment->shouldReceive('getAttribute')->with('finished_at')->andReturn($finished);
    }
}
