<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs\DeployProject;

use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\DeployProject\LogFormatter;
use REBELinBLUE\Deployer\Jobs\DeployProject\SendFileToServer;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\DeployProject\SendFileToServer
 * @fixme Figure out how to test the callback
 */
class SendFileToServerTest extends TestCase
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
    private $local_file;

    /**
     * @var string
     */
    private $remote_file;

    /**
     * @var string
     */
    private $key;

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function setUp()
    {
        parent::setUp();

        $port          = 22;
        $deployment_id = 12987;
        $user          = 'deployer';
        $ip_address    = '127.0.0.1';
        $key           = 'a-private-ssh-key';
        $remote_file   = '/var/www/project/file.txt';
        $local_file    = '/tmp/local-file.txt';

        $server = m::mock(Server::class);
        $server->shouldReceive('getAttribute')->with('port')->andReturn($port);
        $server->shouldReceive('getAttribute')->with('user')->andReturn($user);
        $server->shouldReceive('getAttribute')->with('ip_address')->andReturn($ip_address);

        $log = m::mock(ServerLog::class);
        $log->shouldReceive('getAttribute')->with('server')->andReturn($server);

        $formatter = m::mock(LogFormatter::class);

        $deployment = m::mock(Deployment::class);
        $deployment->shouldReceive('getAttribute')->with('id')->andReturn($deployment_id);

        $process = m::mock(Process::class);
        $process->shouldReceive('setScript')->once()->with('deploy.SendFileToServer', [
            'deployment'  => $deployment_id,
            'port'        => $port,
            'private_key' => $key,
            'local_file'  => $local_file,
            'remote_file' => $remote_file,
            'username'    => $user,
            'ip_address'  => $ip_address,
        ])->andReturnSelf();
        $process->shouldReceive('run')->once();

        $this->deployment  = $deployment;
        $this->log         = $log;
        $this->process     = $process;
        $this->key         = $key;
        $this->remote_file = $remote_file;
        $this->local_file  = $local_file;
        $this->formatter   = $formatter;
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
        $job = new SendFileToServer(
            $this->deployment,
            $this->log,
            $this->local_file,
            $this->remote_file,
            $this->key
        );

        $job->handle($this->process, $this->formatter);
    }
}
