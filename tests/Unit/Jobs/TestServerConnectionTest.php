<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Closure;
use Mockery as m;
use REBELinBLUE\Deployer\Jobs\DeployProject\LogFormatter;
use REBELinBLUE\Deployer\Jobs\TestServerConnection;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Process\Process as SymfonyProcess;

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

    /** @var LogFormatter */
    private $formatter;

    public function setUpExpections()
    {
        parent::setUp();

        $key_file    = '/tmp/sshkey';
        $private_key = 'a-private-key';
        $server_id   = 100;
        $project_id  = 12;
        $clean_path  = '/var/www/deployer';
        $prefix      = '100_12';

        $project = m::mock(Project::class);
        $project->shouldReceive('getAttribute')->once()->with('private_key')->andReturn($private_key);

        $server = m::mock(Server::class);
        $server->shouldReceive('getAttribute')->once()->with('project')->andReturn($project);
        $server->shouldReceive('getAttribute')->atLeast(1)->with('id')->andReturn($server_id);
        $server->shouldReceive('getAttribute')->once()->with('project_id')->andReturn($project_id);
        $server->shouldReceive('getAttribute')->once()->with('clean_path')->andReturn($clean_path);
        $server->shouldReceive('setAttribute')->once()->with('status', Server::TESTING);
        $server->shouldReceive('setAttribute')->once()->with('connect_log', null);

        $formatter = m::mock(LogFormatter::class);

        $filesystem = m::mock(Filesystem::class);
        $filesystem->shouldReceive('tempnam')->once()->with(storage_path('app/tmp/'), 'key')->andReturn($key_file);
        $filesystem->shouldReceive('put')->once()->with($key_file, $private_key);
        $filesystem->shouldReceive('chmod')->once()->with($key_file, 0600);
        $filesystem->shouldReceive('delete')->once()->with($key_file);

        $process = m::mock(Process::class);
        $process->shouldReceive('setScript')->once()->with('TestServerConnection', [
            'server_id'      => 100,
            'project_path'   => $clean_path,
            'test_file'      => $prefix . '_testing_deployer.txt',
            'test_directory' => $prefix . '_testing_deployer_dir',
        ])->andReturnSelf();
        $process->shouldReceive('setServer')->once()->with($server, $key_file)->andReturnSelf();

        $this->server      = $server;
        $this->process     = $process;
        $this->filesystem  = $filesystem;
        $this->formatter   = $formatter;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleSuccessful()
    {
        $this->setUpExpections();

        $errorIn   = 'a-line-of-error-output';
        $successIn = 'a-line-of-success-output';

        $this->process->shouldReceive('run')->with(m::on(function ($callback) use ($errorIn, $successIn) {
            $callback(SymfonyProcess::OUT, $successIn);
            $callback(SymfonyProcess::ERR, $errorIn);
            $this->assertInstanceOf(Closure::class, $callback);

            return true;
        }));

        $this->formatter->shouldReceive('info')->with($successIn)->andReturn('info');
        $this->formatter->shouldReceive('error')->with($errorIn)->andReturn(' - error');

        $this->process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $this->server->shouldReceive('setAttribute')->once()->with('status', Server::SUCCESSFUL);

        // Log should be cleared on success
        $this->server->shouldReceive('setAttribute')->once()->with('connect_log', 'info');
        $this->server->shouldReceive('setAttribute')->once()->with('connect_log', 'info - error');
        $this->server->shouldReceive('setAttribute')->once()->with('connect_log', null);

        $this->server->shouldReceive('save')->times(4);

        $job = new TestServerConnection($this->server);
        $job->handle($this->process, $this->filesystem, $this->formatter);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleUnsuccessful()
    {
        $this->setUpExpections();

        $errorIn   = 'a-line-of-error-output';
        $successIn = 'a-line-of-success-output';

        $this->process->shouldReceive('run')->with(m::on(function ($callback) use ($errorIn, $successIn) {
            $callback(SymfonyProcess::ERR, $errorIn);
            $callback(SymfonyProcess::OUT, $successIn);
            $this->assertInstanceOf(Closure::class, $callback);

            return true;
        }));

        $this->formatter->shouldReceive('error')->with($errorIn)->andReturn('error' . PHP_EOL);
        $this->formatter->shouldReceive('info')->with($successIn)->andReturn('info');

        $this->process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $this->server->shouldReceive('setAttribute')->once()->with('status', Server::FAILED);
        $this->server->shouldReceive('setAttribute')->once()->with('connect_log', 'error' . PHP_EOL);
        $this->server->shouldReceive('setAttribute')->once()->with('connect_log', 'error' . PHP_EOL . 'info');
        $this->server->shouldReceive('save')->times(4);

        $job = new TestServerConnection($this->server);
        $job->handle($this->process, $this->filesystem, $this->formatter);
    }

    /**
     * @covers ::__construct
     */
    public function testItHasUnlimitedTimeout()
    {
        $server = m::mock(Server::class);
        $job    = new TestServerConnection($server);

        $this->assertSame(0, $job->timeout);
    }
}
