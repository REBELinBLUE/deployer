<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use BackupManager\Laravel\DbBackupCommand;
use Carbon\Carbon;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Console\Command;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Foundation\Application;
use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\Installer\EnvFile;
use REBELinBLUE\Deployer\Console\Commands\Installer\Requirements;
use REBELinBLUE\Deployer\Console\Commands\UpdateApp;
use REBELinBLUE\Deployer\Events\RestartSocketServer;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\UpdateApp
 * @fixme: ensure it doesn't bring the app up if it is down before running the command!
 */
class UpdateAppTest extends TestCase
{
    protected $filesystem;
    private $console;
    private $config;
    private $repository;
    private $laravel;
    private $requirements;
    private $env;

    public function setUp()
    {
        parent::setUp();

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $this->console      = $console;
        $this->config       = m::mock(ConfigRepository::class);
        $this->filesystem   = m::mock(Filesystem::class);
        $this->repository   = m::mock(DeploymentRepositoryInterface::class);
        $this->requirements = m::mock(Requirements::class);
        $this->env          = m::mock(EnvFile::class);
        $this->laravel      = m::mock(Application::class)->makePartial();

        $this->laravel->shouldReceive('make')->andReturnUsing(function ($arg) {
            return $this->app->make($arg);
        });
    }

    /**
     * @dataProvider provideAppKey
     * @covers ::__construct
     * @covers ::handle
     * @covers ::verifyInstalled
     * @covers ::checkCanInstall
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles::failure
     */
    public function testVerifyInstalled($key)
    {
        $this->config->shouldReceive('get')->with('app.key')->andReturn($key);

        $tester = $this->runCommand();
        $output = $tester->getDisplay();

        $this->assertContains('Deployer has not been installed', $output);
        $this->assertContains('php artisan app:install', $output);
        $this->assertSame(-1, $tester->getStatusCode());
    }

    public function provideAppKey()
    {
        return array_chunk([
            false,
            'SomeRandomString',
        ], 1);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::hasDeprecatedConfig
     * @covers ::checkCanInstall
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles::failure
     */
    public function testHasDeprecatedConfig()
    {
        $this->config->shouldReceive('get')->with('app.key')->andReturn('a-valid-key');
        $this->filesystem->shouldReceive('get')->with(base_path('.env'))->andReturn('DB_TYPE=mysql');

        $tester = $this->runCommand();
        $output = $tester->getDisplay();

        $this->assertContains('Update not complete!', $output);
        $this->assertContains('DB_TYPE', $output);
        $this->assertContains('DB_CONNECTION', $output);
        $this->assertSame(-1, $tester->getStatusCode());
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::composerOutdated
     * @covers ::checkCanInstall
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles::failure
     */
    public function testComposerOutdated()
    {
        $now = Carbon::create(2017, 2, 1, 12, 5, 15, 'UTC');
        Carbon::setTestNow($now);

        $modified = Carbon::create(2017, 2, 1, 11, 5, 0, 'UTC');

        $this->config->shouldReceive('get')->with('app.key')->andReturn('a-valid-key');
        $this->filesystem->shouldReceive('get')->with(base_path('.env'))->andReturn('config-file-content');

        $this->filesystem->shouldReceive('lastModified')
                         ->with(base_path('vendor/autoload.php'))
                         ->andReturn($modified->timestamp);

        $tester = $this->runCommand();
        $output = $tester->getDisplay();

        $this->assertContains('Update not complete!', $output);
        $this->assertContains('composer install --no-suggest --no-dev -o', $output);
        $this->assertSame(-1, $tester->getStatusCode());
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::nodeOutdated
     * @covers ::checkCanInstall
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles::failure
     */
    public function testNodeOutdated()
    {
        $now = Carbon::create(2017, 2, 1, 12, 5, 15, 'UTC');
        Carbon::setTestNow($now);

        $modified = Carbon::create(2017, 2, 1, 11, 5, 0, 'UTC');

        $this->config->shouldReceive('get')->with('app.key')->andReturn('a-valid-key');
        $this->filesystem->shouldReceive('get')->with(base_path('.env'))->andReturn('config-file-content');

        $this->filesystem->shouldReceive('lastModified')
                         ->with(base_path('vendor/autoload.php'))
                         ->andReturn($now->timestamp);

        $this->filesystem->shouldReceive('lastModified')
                         ->with(base_path('node_modules/.install'))
                         ->andReturn($modified->timestamp);

        $tester = $this->runCommand();
        $output = $tester->getDisplay();

        $this->assertContains('Update not complete!', $output);
        $this->assertContains('npm install --production', $output);
        $this->assertSame(-1, $tester->getStatusCode());
    }

    /**
     * @dataProvider provideDeploymentCount
     * @covers ::__construct
     * @covers ::handle
     * @covers ::hasRunningDeployments
     * @covers ::checkCanInstall
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles::failure
     */
    public function testHasRunningDeployments($running, $pending)
    {
        $now = Carbon::create(2017, 2, 1, 12, 5, 15, 'UTC');
        Carbon::setTestNow($now);

        $this->config->shouldReceive('get')->with('app.key')->andReturn('a-valid-key');
        $this->filesystem->shouldReceive('get')->with(base_path('.env'))->andReturn('config-file-content');
        $this->filesystem->shouldReceive('lastModified')->andReturn($now->timestamp);

        $this->repository->shouldReceive('getRunning->count')->andReturn($running);
        $this->repository->shouldReceive('getPending->count')->andReturn($pending);

        $tester = $this->runCommand();
        $output = $tester->getDisplay();

        $this->assertContains('Deployments in progress', $output);
        $this->assertSame(-1, $tester->getStatusCode());
    }

    public function provideDeploymentCount()
    {
        return [
            [0, 10],
            [10, 0],
            [10, 10],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::checkCanInstall
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles::failure
     */
    public function testCheckRequirements()
    {
        $now = Carbon::create(2017, 2, 1, 12, 5, 15, 'UTC');
        Carbon::setTestNow($now);

        $this->config->shouldReceive('get')->with('app.key')->andReturn('a-valid-key');
        $this->filesystem->shouldReceive('get')->with(base_path('.env'))->andReturn('config-file-content');
        $this->filesystem->shouldReceive('lastModified')->andReturn($now->timestamp);
        $this->repository->shouldReceive('getRunning->count')->andReturn(0);
        $this->repository->shouldReceive('getPending->count')->andReturn(0);
        $this->requirements->shouldReceive('check')->with(m::type(UpdateApp::class))->andReturn(false);

        $tester = $this->runCommand();

        $this->assertSame(-1, $tester->getStatusCode());
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::composerOutdated
     * @covers ::nodeOutdated
     * @covers ::checkCanInstall
     * @covers ::verifyInstalled
     * @covers ::hasDeprecatedConfig
     * @covers ::checkCanInstall
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles::failure
     */
    public function testHandleWhenConfirmationDenied()
    {
        $this->mockChecks();

        $this->console->shouldNotReceive('find')->with('down');

        $this->laravel->shouldReceive('isDownForMaintenance')->andReturn(false);

        $tester = $this->runCommand($this->laravel, ['no']);

        $this->assertContains('Switch to maintenance mode now?', $tester->getDisplay());
        $this->assertSame(-1, $tester->getStatusCode());
    }

    /**
     * @covers \REBELinBLUE\Deployer\Console\Commands\UpdateApp
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles
     */
    public function testHandleSuccessful()
    {
        $this->mockChecks();

        $this->config->shouldReceive('get')->with('database.default')->andReturn('deployer');

        $this->laravel->shouldReceive('isDownForMaintenance')->andReturn(false);
        $this->laravel->shouldReceive('environment')->with('local')->andReturn(false);

        $this->env->shouldReceive('update')->andReturn(true);

        $command = m::mock(Command::class);
        $command->shouldReceive('run');

        $migrate = m::mock(MigrateCommand::class);
        $migrate->shouldReceive('run')->with(m::on(function (ArrayInput $arg) {
            // FIXME: Is there a better way to do this?
            $this->assertTrue($arg->getParameterOption('--force'));

            return true;
        }), m::any());

        $backup = m::mock(DbBackupCommand::class);
        $backup->shouldReceive('run')->with(m::on(function (ArrayInput $arg) {
            // FIXME: Is there a better way to do this?
            $this->assertSame('deployer', $arg->getParameterOption('--database'));
            $this->assertSame('local', $arg->getParameterOption('--destination'));
            $this->assertSame('2017-02-01 12.05.15', $arg->getParameterOption('--destinationPath'));
            $this->assertSame('gzip', $arg->getParameterOption('--compression'));

            return true;
        }), m::any());

        // FIXME: Is there a cleaner way to do this?
        $this->console->shouldReceive('find')->with('down')->andReturn($command);
        $this->console->shouldReceive('find')->with('db:backup')->andReturn($backup);
        $this->console->shouldReceive('find')->with('clear-compiled')->andReturn($command);
        $this->console->shouldReceive('find')->with('cache:clear')->andReturn($command);
        $this->console->shouldReceive('find')->with('route:clear')->andReturn($command);
        $this->console->shouldReceive('find')->with('config:clear')->andReturn($command);
        $this->console->shouldReceive('find')->with('view:clear')->andReturn($command);
        $this->console->shouldReceive('find')->with('migrate')->andReturn($migrate);
        $this->console->shouldReceive('find')->with('config:cache')->andReturn($command);
        $this->console->shouldReceive('find')->with('route:cache')->andReturn($command);
        $this->console->shouldReceive('find')->with('queue:flush')->andReturn($command);
        $this->console->shouldReceive('find')->with('queue:restart')->andReturn($command);
        $this->console->shouldReceive('find')->with('up')->andReturn($command);

        $this->expectsEvents(RestartSocketServer::class);

        $tester = $this->runCommand($this->laravel, ['yes']);

        $output = $tester->getDisplay();

        $this->assertContains('Switch to maintenance mode now?', $output);
        $this->assertContains('Updating configuration file', $output);
        $this->assertContains('Restarting the queue', $output);
        $this->assertContains('Restarting the socket server', $output);
        $this->assertSame(0, $tester->getStatusCode());
    }

    private function runCommand($app = null, array $inputs = [])
    {
        $this->app->instance(EnvFile::class, $this->env);
        $this->app->instance(Requirements::class, $this->requirements);

        $command = new UpdateApp(
            $this->config,
            $this->filesystem,
            $this->repository
        );

        $command->setLaravel($app ?: $this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->setInputs($inputs);
        $tester->execute([
            'command' => 'app:update',
        ]);

        return $tester;
    }

    private function mockChecks()
    {
        $now = Carbon::create(2017, 2, 1, 12, 5, 15, 'UTC');
        Carbon::setTestNow($now);

        $this->config->shouldReceive('get')->with('app.key')->andReturn('a-valid-key');
        $this->filesystem->shouldReceive('get')->once()->with(base_path('.env'))->andReturn('config-file-content');
        $this->filesystem->shouldReceive('lastModified')->andReturn($now->timestamp);

        $this->repository->shouldReceive('getRunning->count')->andReturn(0);
        $this->repository->shouldReceive('getPending->count')->andReturn(0);

        $this->requirements->shouldReceive('check')->with(m::type(UpdateApp::class))->andReturn(true);
    }
}
