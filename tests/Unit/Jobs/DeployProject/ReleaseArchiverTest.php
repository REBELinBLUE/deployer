<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs\DeployProject;

use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\DeployProject\ReleaseArchiver;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\DeployProject\ReleaseArchiver
 */
class ReleaseArchiverTest extends TestCase
{
    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var string
     */
    private $path = 'release.tar.gz';

    public function setUp()
    {
        parent::setUp();

        $deployment_id = 12987;
        $project_id    = 354;
        $release_id    = 20160101175545;
        $commit        = 'a-git-commit-hash';
        $mirror        = '/var/repositories/mirror.git';
        $tmp_dir       = 'clone_354_20160101175545';

        $project = m::mock(Project::class);
        $project->shouldReceive('mirrorPath')->once()->andReturn($mirror);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('id')->andReturn($deployment_id);
        $deployment->shouldReceive('getAttribute')->with('project_id')->andReturn($project_id);
        $deployment->shouldReceive('getAttribute')->with('release_id')->andReturn($release_id);
        $deployment->shouldReceive('getAttribute')->with('commit')->andReturn($commit);
        $deployment->shouldReceive('getAttribute')->with('project')->andReturn($project);

        $process = m::mock(Process::class);
        $process->shouldReceive('setScript')->once()->with('deploy.CreateReleaseArchive', [
            'deployment'      => $deployment_id,
            'mirror_path'     => $mirror,
            'scripts_path'    => resource_path('scripts/'),
            'tmp_path'        => storage_path('app/tmp/' . $tmp_dir),
            'sha'             => $commit,
            'release_archive' => storage_path('app/' . $this->path),
        ])->andReturnSelf();
        $process->shouldReceive('run')->once();

        $this->deployment = $deployment;
        $this->project    = $project;
        $this->process    = $process;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleIsSuccessful()
    {
        $this->process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $this->process->shouldNotReceive('getErrorOutput');

        $this->job();
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleIsUnsuccessful()
    {
        $this->expectException(RuntimeException::class);
        $this->process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $this->process->shouldReceive('getErrorOutput')->once();

        $this->job();
    }

    private function job()
    {
        $job = new ReleaseArchiver($this->deployment, $this->path);
        $job->handle($this->process);
    }
}
