<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs\DeployProject;

use Illuminate\Support\Collection;
use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\DeployProject\CleanupFailedDeployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\DeployProject\CleanupFailedDeployment
 * @todo Test server with deploy_code is false
 */
class CleanupFailedDeploymentTest extends TestCase
{
    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $release_archive;

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function setUp()
    {
        parent::setUp();

        $deployment_id   = 12987;
        $release_id      = 2345678;
        $release_archive = 'release_12345678.tar.gz';
        $key             = 'a-private-ssh-key';
        $clean_path      = '/var/www/deployer';

        $server = m::mock(Server::class);
        $server->shouldReceive('getAttribute')->with('clean_path')->andReturn($clean_path);
        $server->shouldReceive('getAttribute')->with('deploy_code')->andReturn(true);

        $servers = new Collection([$server]);

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->with('servers')->andReturn($servers);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('id')->andReturn($deployment_id);
        $deployment->shouldReceive('getAttribute')->with('release_id')->andReturn($release_id);
        $deployment->shouldReceive('getAttribute')->with('project')->andReturn($project);

        $process = m::mock(Process::class);
        $process->shouldReceive('setScript')->once()->with('deploy.CleanupFailedRelease', [
            'deployment'     => $deployment_id,
            'project_path'   => $clean_path,
            'release_path'   => $clean_path . '/releases/' . $release_id,
            'remote_archive' => $clean_path . '/' . $release_archive,
        ])->andReturnSelf();
        $process->shouldReceive('setServer')->with($server, $key)->once()->andReturnSelf();
        $process->shouldReceive('run')->once();

        $this->deployment      = $deployment;
        $this->process         = $process;
        $this->key             = $key;
        $this->release_archive = $release_archive;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandle()
    {
        $job = new CleanupFailedDeployment(
            $this->deployment,
            $this->release_archive,
            $this->key
        );

        $job->handle($this->process);
    }
}
