<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Illuminate\Console\Command;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\DebugApp;
use REBELinBLUE\Deployer\Events\RestartSocketServer;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\DebugApp
 */
class DebugAppTest extends TestCase
{
    public function testHandleOn()
    {
        $input = <<< EOF
APP_DEBUG=false
APP_LOG_LEVEL=error
APP_KEY=some-key
EOF;

        $output = <<< EOF
APP_DEBUG=true
APP_LOG_LEVEL=debug
APP_KEY=some-key
EOF;

        $tester = $this->runCommandTest($input, $output, 'on');

        $this->assertContains('Enabling debug mode', $tester->getDisplay());
    }

    public function testHandleOff()
    {
        $input = <<< EOF
APP_DEBUG=true
APP_LOG_LEVEL=debug
APP_KEY=some-key
EOF;

        $output = <<< EOF
APP_DEBUG=false
APP_LOG_LEVEL=error
APP_KEY=some-key
EOF;

        $tester = $this->runCommandTest($input, $output, 'off');

        $this->assertContains('Disabling debug mode', $tester->getDisplay());
    }

    private function runCommandTest($input, $output, $status)
    {
        $env = base_path('.env');

        $filesystem = m::mock(Filesystem::class);
        $filesystem->shouldReceive('get')->with($env)->andReturn($input);
        $filesystem->shouldReceive('put')->with($env, $output);

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $command = m::mock(Command::class);
        $command->shouldReceive('run');

        $console->shouldReceive('find')->with('config:clear')->andReturn($command);
        $console->shouldReceive('find')->with('queue:restart')->andReturn($command);

        $this->expectsEvents(RestartSocketServer::class);

        $command = new DebugApp($filesystem);
        $command->setLaravel($this->app);
        $command->setApplication($console);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => 'app:debug',
            'status'  => $status,
        ]);

        return $tester;
    }
}
