<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands\Installer;

use Exception;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Console\Command;
use Illuminate\Queue\QueueManager;
use Illuminate\Redis\RedisManager;
use Mockery as m;
use phpmock\mockery\PHPMockery as phpm;
use REBELinBLUE\Deployer\Console\Commands\Installer\Requirements;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\Installer\Requirements
 */
class RequirementsTest extends TestCase
{
    private $config;
    private $process;
    private $queue;
    private $redis;
    private $command;
    private $filesystem;

    public function setUp()
    {
        parent::setUp();

        $this->process    = m::mock(Process::class);
        $this->queue      = m::mock(QueueManager::class);
        $this->redis      = m::mock(RedisManager::class);
        $this->config     = m::mock(ConfigRepository::class);
        $this->command    = m::mock(Command::class);
        $this->filesystem = m::mock(Filesystem::class);
    }

    /**
     * @covers ::__construct
     * @covers ::check
     * @covers ::versionCheck
     * @covers ::extensionCheck
     * @covers ::hasDatabaseDriver
     * @covers ::disabledFunctionCheck
     * @covers ::requiredSystemCommands
     * @covers ::nodeJsCommand
     * @covers ::checkPermissions
     * @covers ::checkRedisConnection
     * @covers ::checkQueueConnection
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\GetAvailableOptions::getDatabaseDrivers
     */
    public function testCheckFails()
    {
        $namespace = 'REBELinBLUE\Deployer\Console\Commands\Installer';

        // Version check failure
        phpm::mock($namespace, 'version_compare')->withAnyArgs()->andReturn(false);

        // Extension check failure
        phpm::mock($namespace, 'extension_loaded')->withAnyArgs()->andReturn(false);

        // Disabled function check failure
        phpm::mock($namespace, 'function_exists')->withAnyArgs()->andReturn(false);

        // Check PDO drivers
        phpm::mock('REBELinBLUE\Deployer\Console\Commands\Traits', 'pdo_drivers')->andReturn(['sqlserv']);

        $this->process->shouldReceive('setCommandLine');
        $this->process->shouldReceive('setTimeout');
        $this->process->shouldReceive('run');
        $this->process->shouldReceive('isSuccessful')->andReturn(false);

        $this->filesystem->shouldReceive('isWritable')->andReturn(false);
        $this->redis->shouldReceive('connection->ping')->andThrow(Exception::class);
        $this->config->shouldReceive('get')->with('queue.default')->andReturn('beanstalkd');
        $this->queue->shouldReceive('connection->getPheanstalk->getConnection->isServiceListening')->andReturn(false);

        $output = '';

        $callback = m::on(function ($message) use (&$output) {
            $output .= $message . PHP_EOL;

            return true;
        });

        $this->command->shouldReceive('error')->with($callback);
        $this->command->shouldReceive('line')->with($callback);
        $this->command->shouldReceive('block')->with($callback);

        $requirements = new Requirements($this->process, $this->config, $this->redis, $this->queue, $this->filesystem);
        $actual       = $requirements->check($this->command);

        $this->assertContains('PHP 7.0.8 or higher is required', $output);
        $this->assertContains('Extension required: PDO, curl, gd, json, mbstring, openssl, tokenizer', $output);
        $this->assertContains('Function required: "proc_open". Is it disabled in php.ini?', $output);
        $this->assertContains('At least 1 PDO driver is required. Either sqlite, mysql or pgsql', $output);
        $this->assertContains('Commands not found: bash, git, gzip, php, rsync, scp, ssh, ssh-keygen, tar', $output);
        $this->assertContains('node.js was not found', $output);
        $this->assertContains('Redis is not running', $output);
        $this->assertContains('Beanstalkd is not running', $output);
        $this->assertContains('Deployer cannot be installed. Please review the errors above', $output);
        $this->assertRegExp('/(.*) is not writable/', $output);

        $this->assertFalse($actual);
    }

    /**
     * @covers ::__construct
     * @covers ::check
     * @covers ::versionCheck
     * @covers ::extensionCheck
     * @covers ::hasDatabaseDriver
     * @covers ::disabledFunctionCheck
     * @covers ::requiredSystemCommands
     * @covers ::nodeJsCommand
     * @covers ::checkPermissions
     * @covers ::checkRedisConnection
     * @covers ::checkQueueConnection
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\GetAvailableOptions::getDatabaseDrivers
     */
    public function testCheck()
    {
        $namespace = 'REBELinBLUE\Deployer\Console\Commands\Installer';

        // Version check failure
        phpm::mock($namespace, 'version_compare')->withAnyArgs()->andReturn(true);

        // Extension check failure
        phpm::mock($namespace, 'extension_loaded')->withAnyArgs()->andReturn(true);

        // Disabled function check failure
        phpm::mock($namespace, 'function_exists')->withAnyArgs()->andReturn(true);

        // Check PDO drivers
        phpm::mock('REBELinBLUE\Deployer\Console\Commands\Traits', 'pdo_drivers')->andReturn(['mysql']);

        $this->process->shouldReceive('setCommandLine');
        $this->process->shouldReceive('setTimeout');
        $this->process->shouldReceive('run');
        $this->process->shouldReceive('isSuccessful')->andReturn(true);

        $this->filesystem->shouldReceive('isWritable')->andReturn(true);
        $this->redis->shouldReceive('connection->ping')->andReturn(true);
        $this->config->shouldReceive('get')->with('queue.default')->andReturn('beanstalkd');
        $this->queue->shouldReceive('connection->getPheanstalk->getConnection->isServiceListening')->andReturn(true);

        $this->command->shouldNotReceive('error');
        $this->command->shouldNotReceive('line');
        $this->command->shouldNotReceive('block');

        $requirements = new Requirements($this->process, $this->config, $this->redis, $this->queue, $this->filesystem);
        $actual       = $requirements->check($this->command);

        $this->assertTrue($actual);
    }
}
