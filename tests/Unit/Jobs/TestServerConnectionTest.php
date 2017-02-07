<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Exception;
use Mockery as m;
use REBELinBLUE\Deployer\Jobs\TestServerConnection;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\TestServerConnection
 */
class TestServerConnectionTest extends TestCase
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function setUp()
    {
        parent::setUp();

        $key_file    = '/tmp/sshkey';
        $private_key = 'a-private-key';
        $server_id   = 100;
        $clean_path  = '/var/www/deployer';

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('private_key')->andReturn($private_key);

        $server = m::mock(Server::class);
        $server->shouldReceive('getAttribute')->once()->with('project')->andReturn($project);
        $server->shouldReceive('getAttribute')->once()->with('id')->andReturn($server_id);
        $server->shouldReceive('getAttribute')->once()->with('clean_path')->andReturn($clean_path);
        $server->shouldReceive('setAttribute')->once()->with('status', Server::TESTING);
        $server->shouldReceive('save')->twice();

        $filesystem = m::mock(Filesystem::class);
        $filesystem->shouldReceive('tempnam')->once()->with(storage_path('app/tmp/'), 'key')->andReturn($key_file);
        $filesystem->shouldReceive('put')->once()->with($key_file, $private_key);
        $filesystem->shouldReceive('chmod')->once()->with($key_file, 0600);
        $filesystem->shouldReceive('delete')->once()->with($key_file);

        $process = m::mock(Process::class);
        $process->shouldReceive('setScript')->once()->with('TestServerConnection', [
            'server_id'      => 100,
            'project_path'   => $clean_path,
            'test_file'      => time() . '_testing_deployer.txt',
            'test_directory' => time() . '_testing_deployer_dir',
        ])->andReturnSelf();
        $process->shouldReceive('setServer')->once()->with($server, $key_file)->andReturnSelf();

        $this->server     = $server;
        $this->process    = $process;
        $this->filesystem = $filesystem;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleSuccessful()
    {
        $this->process->shouldReceive('run')->once();
        $this->process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $this->server->shouldReceive('setAttribute')->once()->with('status', Server::SUCCESSFUL);

        $job = new TestServerConnection($this->server);
        $job->handle($this->process, $this->filesystem);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleUnsuccessful()
    {
        $this->process->shouldReceive('run')->once();
        $this->process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $this->server->shouldReceive('setAttribute')->once()->with('status', Server::FAILED);

        $job = new TestServerConnection($this->server);
        $job->handle($this->process, $this->filesystem);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleDealsWithExceptions()
    {
        $this->process->shouldReceive('run')->andThrow(Exception::class);
        $this->server->shouldReceive('setAttribute')->once()->with('status', Server::FAILED);

        $job = new TestServerConnection($this->server);
        $job->handle($this->process, $this->filesystem);
    }
}
