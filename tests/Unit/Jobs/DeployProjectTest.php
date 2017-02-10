<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Events\DeploymentFinished;
use REBELinBLUE\Deployer\Jobs\DeployProject\ReleaseArchiver;
use REBELinBLUE\Deployer\Jobs\DeployProject\RunDeploymentStep;
use REBELinBLUE\Deployer\Jobs\DeployProject\UpdateRepositoryInfo;
use REBELinBLUE\Deployer\Jobs\UpdateGitMirror;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Jobs\DeployProject;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\DeployProject
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

    public function setUp()
    {
        parent::setUp();

        $deployment_id = 10;
        $project_id = 1;
        $key_file = '/tmp/ssh.keyfile';
        $private_key = 'a-git-key';
        $release_id = 20170105163412;
        $archive = storage_path('app/1_20170105163412.tar.gz');

        $started = Carbon::create(2017, 1, 5, 16, 34, 12, 'UTC');
        $finished = Carbon::create(2017, 1, 5, 16, 42, 31, 'UTC');

        $steps = new Collection([new DeployStep()]);

        $project = m::mock(Project::class);
        $project->shouldReceive('setAttribute')->with('status', Project::DEPLOYING);
        $project->shouldReceive('getAttribute')->with('private_key')->andReturn($private_key);
        $project->shouldReceive('setAttribute')->with('status', Project::FINISHED);
        $project->shouldReceive('setAttribute')->with('last_run', $finished);
        $project->shouldReceive('save')->twice();

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('id')->andReturn($deployment_id);
        $deployment->shouldReceive('getAttribute')->with('project_id')->andReturn($project_id);
        $deployment->shouldReceive('getAttribute')->with('release_id')->andReturn($release_id);
        $deployment->shouldReceive('freshTimestamp')->once()->andReturn($started);
        $deployment->shouldReceive('freshTimestamp')->once()->andReturn($finished);
        $deployment->shouldReceive('setAttribute')->with('status', Deployment::DEPLOYING);
        $deployment->shouldReceive('setAttribute')->with('started_at', $started);
        $deployment->shouldReceive('getAttribute')->with('project')->andReturn($project);
        $deployment->shouldReceive('getAttribute')->with('steps')->andReturn($steps);
        $deployment->shouldReceive('setAttribute')->with('status', Deployment::COMPLETED);
        $deployment->shouldReceive('getAttribute')->with('status')->andReturn(Deployment::COMPLETED);
        $deployment->shouldReceive('setAttribute')->with('finished_at', $finished);
        $deployment->shouldReceive('getAttribute')->with('finished_at')->andReturn($finished);
        $deployment->shouldReceive('save')->twice();

        $filesystem = m::mock(Filesystem::class);
        $filesystem->shouldReceive('tempnam')->with(storage_path('app/tmp/'), 'key')->andReturn($key_file);
        $filesystem->shouldReceive('put')->with($key_file, $private_key);
        $filesystem->shouldReceive('chmod')->with($key_file, 0600);
        $filesystem->shouldReceive('exists')->with($archive)->andReturn(true);
        $filesystem->shouldReceive('delete')->with([$key_file, $archive]);

        $this->project = $project;
        $this->filesystem = $filesystem;
        $this->deployment = $deployment;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::archive
     */
    public function testHandle()
    {
        $this->expectsJobs([
            UpdateGitMirror::class,
            UpdateRepositoryInfo::class,
            ReleaseArchiver::class,
            RunDeploymentStep::class,
        ]);

        $this->expectsEvents(DeploymentFinished::class);

        $job = new DeployProject($this->deployment);
        $job->handle($this->filesystem);
    }
}
