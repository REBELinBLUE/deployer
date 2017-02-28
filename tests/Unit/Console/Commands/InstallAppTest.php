<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\KeyGenerateCommand;
use Illuminate\Foundation\Console\OptimizeCommand;
use MicheleAngioni\MultiLanguage\LanguageManager;
use Mockery as m;
use phpmock\mockery\PHPMockery as phpm;
use REBELinBLUE\Deployer\Console\Commands\InstallApp;
use REBELinBLUE\Deployer\Console\Commands\Installer\EnvFile;
use REBELinBLUE\Deployer\Console\Commands\Installer\Requirements;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Token\TokenGenerator;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\InstallApp
 */
class InstallAppTest extends TestCase
{
    protected $filesystem;
    private $console;
    private $config;
    private $generator;
    private $requirements;
    private $laravel;
    private $env;
    private $builder;
    private $validator;
    private $manager;

    public function setUp()
    {
        parent::setUp();

        $console = m::mock(ConsoleApplication::class)->makePartial();
        $console->__construct();

        $this->console       = $console;
        $this->requirements  = m::mock(Requirements::class);
        $this->config        = m::mock(ConfigRepository::class);
        $this->filesystem    = m::mock(Filesystem::class);
        $this->generator     = m::mock(TokenGenerator::class);
        $this->env           = m::mock(EnvFile::class);
        $this->builder       = m::mock(ProcessBuilder::class);
        $this->validator     = m::mock(ValidationFactory::class);
        $this->manager       = m::mock(LanguageManager::class);
        $this->laravel       = m::mock(Application::class)->makePartial();

        $this->laravel->shouldReceive('make')->andReturnUsing(function ($arg) {
            return $this->app->make($arg);
        });
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::verifyNotInstalled
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles::failure
     */
    public function testVerifyNotInstalled()
    {
        $this->config->shouldReceive('get')->with('app.key')->andReturn('an-existing-key');

        $tester = $this->runCommand();
        $output = $tester->getDisplay();

        $this->assertContains('already installed Deployer', $output);
        $this->assertContains('php artisan app:update', $output);
        $this->assertSame(-1, $tester->getStatusCode());
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::verifyNotInstalled
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles::failure
     */
    public function testCheckRequirements()
    {
        $this->config->shouldReceive('get')->with('app.key')->andReturn(false);
        $this->requirements->shouldReceive('check')->with(m::type(InstallApp::class))->andReturn(false);

        $tester = $this->runCommand();

        $this->assertSame(-1, $tester->getStatusCode());
    }

    /**
     * @covers \REBELinBLUE\Deployer\Console\Commands\InstallApp
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\AskAndValidate
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\GetAvailableOptions
     * @covers \REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles
     */
    public function testHandleSuccessful()
    {
        // FIXME: Clean up, lots of duplication

        $this->config->shouldReceive('get')->with('app.key')->andReturn(false);
        $this->requirements->shouldReceive('check')->with(m::type(InstallApp::class))->andReturn(true);

        $command = m::mock(Command::class);
        $command->shouldReceive('run');

        $key = m::mock(KeyGenerateCommand::class);
        $key->shouldReceive('run')->with(m::on(function (ArrayInput $arg) {
            $this->assertTrue($arg->getParameterOption('--force'));

            return true;
        }), m::any());

        $optimize = m::mock(OptimizeCommand::class);
        $optimize->shouldReceive('run')->with(m::on(function (ArrayInput $arg) {
            $this->assertTrue($arg->getParameterOption('--force'));

            return true;
        }), m::any());

        $this->console->shouldReceive('find')->times(2)->with('clear-compiled')->andReturn($command);
        $this->console->shouldReceive('find')->times(2)->with('cache:clear')->andReturn($command);
        $this->console->shouldReceive('find')->times(2)->with('route:clear')->andReturn($command);
        $this->console->shouldReceive('find')->times(2)->with('config:clear')->andReturn($command);
        $this->console->shouldReceive('find')->times(2)->with('view:clear')->andReturn($command);
        $this->console->shouldReceive('find')->once()->with('key:generate')->andReturn($key);
        $this->console->shouldReceive('find')->once()->with('optimize')->andReturn($optimize);
        $this->console->shouldReceive('find')->once()->with('config:cache')->andReturn($command);
        $this->console->shouldReceive('find')->once()->with('route:cache')->andReturn($command);

        $env                = base_path('.env');
        $dist               = base_path('.env.dist');
        $expectedToken      = 'a-random-app-key';
        $expectedName       = 'Admin';
        $expectedEmail      = 'admin@example.com';
        $expectedPassword   = 'a-password-input';
        $expectedHipchatUrl = 'http://hooks.hipchat.com';
        $expectedFrom       = 'deployer@example.com';
        $expectedAppUrl     = 'https://localhost';
        $expectedKey        = '/var/ssl/private-key';
        $expectedCert       = '/var/ssl/cert';
        $expectedCa         = '/var/ssl/ca';

        $expectedConfig = [
            'db' => [
                'connection' => 'sqlite',
            ],
            'app' => [
                'url'      => $expectedAppUrl,
                'timezone' => 'Europe/London',
                'socket'   => $expectedAppUrl . ':6001',
                'ssl'      => [
                    'key_file'     => $expectedKey,
                    'key_password' => 'key-password',
                    'cert_file'    => $expectedCert,
                    'ca_file'      => $expectedCa,
                ],
                'locale' => 'en',
            ],
            'hipchat' => [
                'token' => 'a-hipchat-token',
                'url'   => $expectedHipchatUrl,
            ],
            'twilio' => [
                'account_sid' => 'twilio-sid',
                'auth_token'  => 'twilio-token',
                'from'        => '+44770812345678',
            ],
            'mail' => [
                'host'         => 'localhost',
                'port'         => '25',
                'username'     => 'mailuser',
                'password'     => 'mailpassword',
                'from_name'    => 'Deployer',
                'from_address' => $expectedFrom,
                'driver'       => 'smtp',
            ],
            'jwt' => [
                'secret' => $expectedToken,
            ],
        ];

        $this->filesystem->shouldReceive('exists')->with($env)->andReturn(false);
        $this->filesystem->shouldReceive('copy')->with($dist, $env);
        $this->config->shouldReceive('set')->with('app.key', 'SomeRandomString');
        $this->laravel->shouldReceive('environment')->with('local')->andReturn(false);

        // PHP drivers
        phpm::mock('REBELinBLUE\Deployer\Console\Commands\Traits', 'pdo_drivers')->andReturn(['sqlite', 'mysql']);

        $this->filesystem->shouldReceive('touch')->with(database_path('database.sqlite'))->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with($expectedKey)->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with($expectedCert)->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with($expectedCa)->andReturn(true);
        $this->generator->shouldReceive('generateRandom')->andReturn($expectedToken);
        $this->env->shouldReceive('save')->with($expectedConfig)->andReturn(true);

        $process = m::mock(Process::class);
        $this->builder->shouldReceive('setPrefix')->with('which')->andReturnSelf();
        $this->builder->shouldReceive('setArguments')->once()->with(['nginx'])->andReturnSelf();
        $process->shouldReceive('isSuccessful')->once()->andReturn(false);

        $this->builder->shouldReceive('setPrefix')->with('php')->andReturnSelf();
        $this->builder->shouldReceive('setArguments')
                      ->once()
                      ->with([base_path('artisan'), 'migrate', '--force'])
                      ->andReturnSelf();

        $this->builder->shouldReceive('setArguments')
                      ->once()
                      ->with([
                          base_path('artisan'),
                          'deployer:create-user',
                          $expectedName,
                          $expectedEmail,
                          $expectedPassword,
                          '--no-email',
                      ])
                      ->andReturnSelf();

        $this->builder->shouldReceive('setWorkingDirectory')->with(base_path())->andReturnSelf();
        $this->builder->shouldReceive('getProcess')->andReturn($process);

        $process->shouldReceive('setTimeout')->with(null)->andReturnSelf();
        $process->shouldReceive('run')->andReturnSelf();
        $process->shouldReceive('stop')->andReturnSelf();
        $process->shouldReceive('isSuccessful')->andReturn(true);

        $rules = m::type('array');
        $this->validator->shouldReceive('make')->with(['url' => $expectedAppUrl], $rules)->andReturnSelf();
        $this->validator->shouldReceive('make')->with(['url' => $expectedHipchatUrl], $rules)->andReturnSelf();
        $this->validator->shouldReceive('make')->with(['port' => 25], $rules)->andReturnSelf();
        $this->validator->shouldReceive('make')->with(['from_address' => $expectedFrom], $rules)->andReturnSelf();
        $this->validator->shouldReceive('make')->with(['email_address' => $expectedEmail], $rules)->andReturnSelf();
        $this->validator->shouldReceive('make')->with(['password' => $expectedPassword], $rules)->andReturnSelf();
        $this->validator->shouldReceive('make')->with(['path' => $expectedKey], $rules)->andReturnSelf();
        $this->validator->shouldReceive('make')->with(['path' => $expectedCert], $rules)->andReturnSelf();
        $this->validator->shouldReceive('make')->with(['path' => $expectedCa], $rules)->andReturnSelf();
        $this->validator->shouldReceive('passes')->andReturn(true);

        $this->manager->shouldReceive('getAvailableLanguages')->andReturn(['en', 'es', 'de', 'ru']);

        $this->config->shouldReceive('get')->with('app.fallback_locale')->andReturn('de');

        $tester = $this->runCommand($this->laravel, [
            // Database details
            'sqlite',
//            'localhost', // Currently can't mock PDO
//            3306,
//            'deployer',
//            'deployer',
//            'secret'

            // App Details
            $expectedAppUrl,
            'Europe',
            'London',
            $expectedAppUrl,
            $expectedKey,
            'key-password',
            $expectedCert,
            $expectedCa,
            'en',

            // Hipchat
            'yes',
            $expectedHipchatUrl,
            'a-hipchat-token',

            // Twilio
            'yes',
            'twilio-sid',
            'twilio-token',
            '+44770812345678',

            // Mail
            'smtp',
            'localhost',
            25,
            'mailuser',
            'mailpassword',
            'Deployer',
            $expectedFrom,

            // Admin details
            $expectedName,
            $expectedEmail,
            $expectedPassword,
        ]);
        $output = $tester->getDisplay();

        //echo $output;

        $this->assertContains('Database details', $output);
        $this->assertContains('Installation details', $output);
        $this->assertContains('Hipchat setup', $output);
        $this->assertContains('Twilio setup', $output);
        $this->assertContains('Email details', $output);
        $this->assertContains('Admin details', $output);
        $this->assertContains('Writing configuration file', $output);
        $this->assertContains('Generating JWT key', $output);
        $this->assertContains('Generating application key', $output);
        $this->assertContains('Running database migrations', $output);
        $this->assertContains('Success!', $output);

        $this->assertSame(0, $tester->getStatusCode());
    }

    private function runCommand($app = null, array $inputs = [])
    {
        $this->app->instance(EnvFile::class, $this->env);
        $this->app->instance(Requirements::class, $this->requirements);

        $command = new InstallApp(
            $this->config,
            $this->filesystem,
            $this->generator,
            $this->builder,
            $this->validator,
            $this->manager
        );

        $command->setLaravel($app ?: $this->app);
        $command->setApplication($this->console);

        $tester = new CommandTester($command);
        $tester->setInputs($inputs);
        try {
            $tester->execute([
                'command' => 'app:install',
            ]);
        } catch (\Exception $error) {
            dd($error);
        }

        return $tester;
    }
}
