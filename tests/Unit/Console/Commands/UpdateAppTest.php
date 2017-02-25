<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use BackupManager\Laravel\DbBackupCommand;
use Carbon\Carbon;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Console\Command;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\OptimizeCommand;
use Mockery as m;
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
    private $console;
    private $filesystem;
    private $config;
    private $repository;
    private $laravel;

    public function setUp()
    {
        parent::setUp();

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $requirements = m::mock(Requirements::class);
        $requirements->shouldReceive('check')->atMost(1)->with(m::type(UpdateApp::class))->andReturn(true);

        $this->config     = m::mock(ConfigRepository::class);
        $this->filesystem = m::mock(Filesystem::class);
        $this->repository = m::mock(DeploymentRepositoryInterface::class);
        $this->laravel    = m::mock(Application::class)->makePartial();

        $this->laravel->shouldReceive('make')->andReturnUsing(function ($arg) {
            return $this->app->make($arg);
        });

        $this->requirements = $requirements;
        $this->console      = $console;
    }

    /**
     * @dataProvider provideAppKey
     * @covers ::__construct
     * @covers ::handle
     * @covers ::verifyInstalled
     * @covers ::checkCanInstall
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
     * @covers ::composerOutdated
     * @covers ::nodeOutdated
     * @covers ::checkCanInstall
     * @covers ::verifyInstalled
     * @covers ::hasDeprecatedConfig
     * @covers ::checkCanInstall
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
     * @covers ::<public>
     * @covers ::<protected>
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\WriteEnvFile::writeEnvFile
     */
    public function testHandleSuccessful()
    {
        $this->mockChecks();

        $this->config->shouldReceive('get')->with('database.default')->andReturn('deployer');

        $this->laravel->shouldReceive('isDownForMaintenance')->andReturn(false);
        $this->laravel->shouldReceive('environment')->with('local')->andReturn(false);

        $prev = base_path('.env.prev');
        $env  = base_path('.env');
        $dist = base_path('.env.dist');

        $my_config = <<< EOF
APP_ENV=local
APP_DEBUG=true

APP_URL=http://deployer.app
SOCKET_URL=http://deployer.app
DB_CONNECTION=sqlite

THIS_SHOULD_BE_REMOVED

MAIL_DRIVER=sendmail
EOF;

        $original_config = <<< EOF
APP_ENV=production
APP_DEBUG=false

# Comment, should be stripped
APP_URL=http://localhost
SOCKET_URL=http://localhost
DB_CONNECTION=sqlite

MAIL_DRIVER=sendmail
FOO_BAR=fizz
EOF;

        $new_config = <<< EOF
APP_ENV=local
APP_DEBUG=true

APP_URL=http://deployer.app
SOCKET_URL=http://deployer.app
DB_CONNECTION=sqlite

MAIL_DRIVER=sendmail
FOO_BAR=fizz
EOF;

        $this->filesystem->shouldReceive('get')->once()->with($env)->andReturn($my_config);
        $this->filesystem->shouldReceive('copy')->with($env, $prev);
        $this->filesystem->shouldReceive('copy')->with($dist, $env);
        $this->filesystem->shouldReceive('get')->once()->with($env)->andReturn($original_config);
        $this->filesystem->shouldReceive('put')->with($env, trim($new_config) . PHP_EOL);
        $this->filesystem->shouldReceive('md5')->with($env)->andReturn('hash');
        $this->filesystem->shouldReceive('md5')->with($prev)->andReturn('hash');
        $this->filesystem->shouldReceive('delete')->with($prev);

        $command = m::mock(Command::class);
        $command->shouldReceive('run');

        $migrate = m::mock(MigrateCommand::class);
        $migrate->shouldReceive('run')->with(m::on(function (ArrayInput $arg) {
            // FIXME: Is there a better way to do this?
            $this->assertTrue($arg->getParameterOption('--force'));

            return true;
        }), m::any());

        $optimize = m::mock(OptimizeCommand::class);
        $optimize->shouldReceive('run')->with(m::on(function (ArrayInput $arg) {
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
        $this->console->shouldReceive('find')->with('optimize')->andReturn($optimize);
        $this->console->shouldReceive('find')->with('config:cache')->andReturn($command);
        $this->console->shouldReceive('find')->with('route:cache')->andReturn($command);
        $this->console->shouldReceive('find')->with('queue:flush')->andReturn($command);
        $this->console->shouldReceive('find')->with('queue:restart')->andReturn($command);
        $this->console->shouldReceive('find')->with('up')->andReturn($command);

        $this->expectsEvents(RestartSocketServer::class);

        $tester = $this->runCommand($this->laravel, ['yes']);

        $output = $tester->getDisplay();

        $this->assertContains('Switch to maintenance mode now?', $output);
        $this->assertContains('Writing configuration file', $output);
        $this->assertContains('Restarting the queue', $output);
        $this->assertContains('Restarting the socket server', $output);
        $this->assertSame(0, $tester->getStatusCode());
    }

    private function runCommand($app = null, array $inputs = [])
    {
        $command = new UpdateApp(
            $this->config,
            $this->filesystem,
            $this->repository,
            $this->requirements
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
    }
}
