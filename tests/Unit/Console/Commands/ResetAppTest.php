<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Foundation\Application;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\ResetApp;
use REBELinBLUE\Deployer\Console\Commands\UpdateApp;
use REBELinBLUE\Deployer\Events\RestartSocketServer;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\ResetApp
 */
class ResetAppTest extends TestCase
{
    private $laravel;
    private $filesystem;
    private $console;

    protected function setUp(): void
    {
        parent::setUp();

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $this->filesystem = m::mock(Filesystem::class);
        $this->laravel    = m::mock(Application::class)->makePartial();

        $this->laravel->shouldReceive('make')->andReturnUsing(function ($arg) {
            return $this->app->make($arg);
        });

        $this->console = $console;
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::verifyNotProduction
     */
    public function testVerifyNotProduction()
    {
        $this->laravel->shouldReceive('environment')->with('local')->andReturn(false);

        $tester = $this->runCommand();
        $output = $tester->getDisplay();

        $this->assertStringContainsString('not in development mode', $output);
        $this->assertStringContainsString('wipe your database', $output);
        $this->assertSame(-1, $tester->getStatusCode());
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::verifyNotProduction
     * @covers ::clearLogs
     * @covers ::restartSocket
     * @covers ::restartQueue
     * @covers ::resetDatabase
     */
    public function testHandle()
    {
        $logs = ['foo.log', 'bar.log', 'cli-2016-08-01.log'];

        $command = m::mock(Command::class);
        $command->shouldReceive('run');

        $update = m::mock(UpdateApp::class);
        $update->shouldReceive('run')->once()->with(m::on(function (ArrayInput $arg) {
            $this->assertTrue($arg->getParameterOption('--no-backup'));

            return true;
        }), m::any());

        $migrate = m::mock(MigrateCommand::class);
        $migrate->shouldReceive('run')->once()->with(m::on(function (ArrayInput $arg) {
            $this->assertTrue($arg->getParameterOption('--force'));

            return true;
        }), m::any());

        $migrate->shouldReceive('run')->once()->with(m::on(function (ArrayInput $arg) {
            $this->assertTrue($arg->getParameterOption('--seed'));
            $this->assertTrue($arg->getParameterOption('--force'));

            return true;
        }), m::any());

        $this->laravel->shouldReceive('environment')->with('local')->andReturn(true);

        $this->console->shouldReceive('find')->with('down')->andReturn($command);
        $this->console->shouldReceive('find')->with('migrate')->andReturn($migrate);
        $this->console->shouldReceive('find')->with('app:update')->andReturn($update);
        $this->console->shouldReceive('find')->with('migrate:refresh')->andReturn($migrate);
        $this->console->shouldReceive('find')->with('queue:flush')->andReturn($command);
        $this->console->shouldReceive('find')->with('queue:restart')->andReturn($command);
        $this->console->shouldReceive('find')->with('up')->andReturn($command);

        $this->filesystem->shouldReceive('touch')->with(base_path('vendor/autoload.php'));
        $this->filesystem->shouldReceive('touch')->with(base_path('node_modules/.install'));
        $this->filesystem->shouldReceive('glob')->with(storage_path('logs') . '/*.log')->andReturn($logs);
        $this->filesystem->shouldReceive('delete')->with($logs);

        $this->expectsEvents(RestartSocketServer::class);

        $tester = $this->runCommand();
        $output = $tester->getDisplay();

        $this->assertStringContainsString('Removing log files', $output);
        $this->assertStringContainsString('Restarting the queue', $output);
        $this->assertStringContainsString('Restarting the socket server', $output);
        $this->assertSame(0, $tester->getStatusCode());
    }

    private function runCommand(array $inputs = []): CommandTester
    {
        $command = new ResetApp($this->filesystem);

        $command->setLaravel($this->laravel);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);

        $this->app->bind(OutputStyle::class, function () use ($tester) {
            return new OutputStyle($tester->getInput(), $tester->getOutput());
        });

        $tester->setInputs($inputs);
        $tester->execute([
            'command' => 'app:reset',
        ]);

        return $tester;
    }
}
