<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use MicheleAngioni\MultiLanguage\LanguageManager;
use PDO;
use REBELinBLUE\Deployer\Console\Commands\Installer\EnvFile;
use REBELinBLUE\Deployer\Console\Commands\Installer\Requirements;
use REBELinBLUE\Deployer\Console\Commands\Traits\AskAndValidate;
use REBELinBLUE\Deployer\Console\Commands\Traits\GetAvailableOptions;
use REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * A console command for prompting for install details.
 */
class InstallApp extends Command
{
    use AskAndValidate, OutputStyles, GetAvailableOptions;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the application and configures the settings';

    /**
     * @var ConfigRepository
     */
    protected $config;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var ProcessBuilder
     */
    private $builder;

    /**
     * @var ValidationFactory
     */
    private $validator;

    /**
     * @var LanguageManager
     */
    private $manager;

    /**
     * InstallApp constructor.
     *
     * @param ConfigRepository        $config
     * @param Filesystem              $filesystem
     * @param TokenGeneratorInterface $tokenGenerator
     * @param ProcessBuilder          $builder
     * @param ValidationFactory       $validator
     * @param LanguageManager         $manager
     */
    public function __construct(
        ConfigRepository $config,
        Filesystem $filesystem,
        TokenGeneratorInterface $tokenGenerator,
        ProcessBuilder $builder,
        ValidationFactory $validator,
        LanguageManager $manager
    ) {
        parent::__construct();

        $this->config         = $config;
        $this->filesystem     = $filesystem;
        $this->tokenGenerator = $tokenGenerator;
        $this->builder        = $builder;
        $this->validator      = $validator;
        $this->manager        = $manager;
    }

    /**
     * Execute the console command.
     *
     * @param  EnvFile      $writer
     * @param  Requirements $requirements
     * @return mixed
     */
    public function handle(EnvFile $writer, Requirements $requirements)
    {
        $this->line('');

        $config = base_path('.env');

        if (!$this->filesystem->exists($config)) {
            $this->filesystem->copy(base_path('.env.dist'), $config);
            $this->config->set('app.key', 'SomeRandomString');
        }

        if (!$this->verifyNotInstalled() || !$requirements->check($this)) {
            return -1;
        }

        $this->clearCaches();

        $this->block(' -- Welcome to Deployer -- ', 'fg=white;bg=green;options=bold');
        $this->line('');

        $this->line('Please answer the following questions:');
        $this->line('');

        $config = $this->restructureConfig([
            'db'      => $this->getDatabaseInformation(),
            'app'     => $this->getInstallInformation(),
            'hipchat' => $this->getHipchatInformation(),
            'twilio'  => $this->getTwilioInformation(),
            'mail'    => $this->getEmailInformation(),
            'jwt'     => [],
        ]);

        $admin = $this->getAdminInformation();

        $config['jwt']['secret'] = $this->generateJWTKey();

        $this->info('Writing configuration file');
        $writer->save($config);

        $this->info('Generating JWT key');
        $this->generateKey();

        $this->migrate();
        $this->createAdminUser($admin['name'], $admin['email'], $admin['password']);

        $this->clearCaches();
        $this->optimize();

        $this->line('');
        $this->line('');

        $this->block('Success! Deployer is now installed', 'fg=black;bg=green');
        $this->line('');
        $this->header('Next steps');
        $this->line('');

        $instructions = [
            'Example configuration files can be found in the <options=bold>examples</> directory',
            'Set up your web server, see either <options=bold>nginx.conf</> or <options=bold>apache.conf</>',
            'Setup the cronjobs, see <options=bold>crontab</>',
            'Setup the socket server & queue runner, see <options=bold>supervisor.conf</> for an example setup',
            'Ensure that <options=bold>storage</> is writable by the webserver',
            'Visit <options=bold>' . $config['app']['url'] . '</> & login with the details you provided to get started',
        ];

        foreach ($instructions as $i => $instruction) {
            if ($i !== 0) {
                $instruction = $i . '. ' . $instruction;
            }

            $this->comment($instruction);
            $this->line('');
        }

        return 0;
    }

    /**
     * Generates a key for JWT.
     *
     * @return string
     */
    protected function generateJWTKey()
    {
        return $this->tokenGenerator->generateRandom(32);
    }

    /**
     * Calls the artisan migrate to set up the database.
     */
    protected function migrate()
    {
        $this->info('Running database migrations');
        $this->line('');

        $process = $this->artisanProcess('migrate', ['--force']);

        $process->run(function ($type, $buffer) {
            $buffer = trim($buffer);
            if (empty($buffer)) {
                return;
            }

            if ($type === Process::OUT) {
                $this->line($buffer);
            } else {
                $this->error($buffer);
            }
        });

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }

        $this->line('');
    }

    /**
     * Clears all Laravel caches.
     */
    protected function clearCaches()
    {
        $this->callSilent('clear-compiled');
        $this->callSilent('cache:clear');
        $this->callSilent('route:clear');
        $this->callSilent('config:clear');
        $this->callSilent('view:clear');
    }

    /**
     * Runs the artisan optimize commands.
     */
    protected function optimize()
    {
        if (!$this->laravel->environment('local')) {
            $this->call('config:cache');
            $this->call('route:cache');
        }
    }

    /**
     * Validates the answer is a URL.
     *
     * @param string $answer
     *
     * @return mixed
     */
    protected function validateUrl($answer)
    {
        $validator = $this->validator->make(['url' => $answer], [
            'url' => 'url',
        ]);

        if (!$validator->passes()) {
            throw new RuntimeException($validator->errors()->first('url'));
        }

        return preg_replace('#/$#', '', $answer);
    }

    /**
     * Change the configuration array generated by the prompt so it matches the config file.
     *
     * @param array $config
     *
     * @return array
     */
    private function restructureConfig(array $config)
    {
        // Move the socket value to the correct key
        if (isset($config['app']['socket'])) {
            $config['socket']['url'] = $config['app']['socket'];
            unset($config['app']['socket']);
        }

        if (isset($config['app']['ssl'])) {
            foreach ($config['app']['ssl'] as $key => $value) {
                $config['socket']['ssl_' . $key] = $value;
            }

            unset($config['app']['ssl']);
        }

        ksort($config);

        return $config;
    }

    /**
     * Prompts for the twilio API details.
     *
     * @return array
     */
    private function getTwilioInformation()
    {
        $this->header('Twilio setup');

        $twilio =  [
            'account_sid' => '',
            'auth_token'  => '',
            'from'        => '',
        ];

        if ($this->confirm('Do you wish to be able to send notifications using Twilio?')) {
            $twilio['account_sid'] = $this->ask('Account SID');
            $twilio['auth_token']  = $this->ask('Auth token');
            $twilio['from']        = $this->ask('Twilio phone number');
        }

        return $twilio;
    }

    /**
     * Prompts for the hipchat API details.
     *
     * @return array
     */
    private function getHipchatInformation()
    {
        $this->header('Hipchat setup');

        $hipchat = [
            'token' => '',
            'url'   => '',
        ];

        if ($this->confirm('Do you wish to be able to send notifications to Hipchat?')) {
            $hipchat['url'] = $this->askAndValidate('Webhook URL', [], function ($answer) {
                return $this->validateUrl($answer);
            });

            $hipchat['token'] = $this->ask('Token');
        }

        return $hipchat;
    }

    /**
     * Calls the artisan key:generate to set the APP_KEY.
     */
    private function generateKey()
    {
        $this->info('Generating application key');
        $this->callSilent('key:generate', ['--force' => true]);
    }

    /**
     * Forks a process to create the admin user.
     *
     * @param string $name
     * @param string $email
     * @param string $password
     */
    private function createAdminUser($name, $email, $password)
    {
        $process = $this->artisanProcess('deployer:create-user', [$name, $email, $password, '--no-email']);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }
    }

    /**
     * Generates a Symfony Process instance for an artisan command.
     *
     * @param string $command
     * @param array  $args
     *
     * @return \Symfony\Component\Process\Process
     */
    private function artisanProcess($command, array $args = [])
    {
        $arguments = array_merge([
            base_path('artisan'),
            $command,
        ], $args, ['--ansi']);

        $this->builder->setPrefix('php');

        return $this->builder->setArguments($arguments)
                             ->setWorkingDirectory(base_path())
                             ->getProcess()
                             ->setTimeout(null);
    }

    /**
     * Prompts the user for the database connection details.
     *
     * @return array
     */
    private function getDatabaseInformation()
    {
        $this->header('Database details');

        $connectionVerified = false;

        $database = [];
        while (!$connectionVerified) {
            // Should we just skip this step if only one driver is available?
            $type = $this->choice('Type', $this->getDatabaseDrivers(), 0);

            $database['connection'] = $type;

            if ($type !== 'sqlite') {
                $defaultPort = $type === 'mysql' ? 3306 : 5432;

                $host = $this->anticipate('Host', ['localhost'], 'localhost');
                $port = $this->anticipate('Port', [$defaultPort], $defaultPort);
                $name = $this->anticipate('Name', ['deployer'], 'deployer');
                $user = $this->ask('Username', 'deployer');
                $pass = $this->secret('Password');

                $database['host']     = $host;
                $database['port']     = $port;
                $database['database'] = $name;
                $database['username'] = $user;
                $database['password'] = $pass;
            }

            $connectionVerified = true;

            //$connectionVerified = $this->verifyDatabaseDetails($database);
        }

        return $database;
    }

    /**
     * Prompts the user for the basic setup information.
     *
     * @return array
     */
    private function getInstallInformation()
    {
        $this->header('Installation details');

        $regions = $this->getTimezoneRegions();
        $locales = $this->manager->getAvailableLanguages();

        $url_callback = function ($answer) {
            return $this->validateUrl($answer);
        };

        $url    = $this->askAndValidate('Application URL ("http://deployer.app" for example)', [], $url_callback);
        $region = $this->choice('Timezone region', array_keys($regions), 0);

        if ($region !== 'UTC') {
            $locations = $this->getTimezoneLocations($regions[$region]);

            $region .= '/' . $this->choice('Timezone location', $locations, 0);
        }

        $socket = $this->askAndValidate('Socket URL', [], $url_callback, $url);

        // If the URL doesn't have : in twice (the first is in the protocol, the second for the port)
        if (substr_count($socket, ':') === 1) {
            // Check if running on nginx, and if not then add it
            $this->builder->setPrefix('which');

            // Something has changed in laravel 5.3 which means calling the migrate command with call() isn't working
            $process = $this->builder->setArguments(['nginx'])
                                     ->setWorkingDirectory(base_path())
                                     ->getProcess()
                                     ->setTimeout(null);

            $process->run();

            if (!$process->isSuccessful()) {
                $socket .= ':6001';
            }
        }

        $path_callback = function ($answer) {
            $validator = $this->validator->make(['path' => $answer], [
                'path' => 'required',
            ]);

            if (!$validator->passes()) {
                throw new RuntimeException($validator->errors()->first('path'));
            }

            if (!$this->filesystem->exists($answer)) {
                throw new RuntimeException('File does not exist');
            }

            return $answer;
        };

        $ssl = null;
        if (substr($socket, 0, 5) === 'https') {
            $ssl = [
                'key_file'     => $this->askAndValidate('SSL key file', [], $path_callback),
                'key_password' => $this->askSecretAndValidate('SSL key password', [], function ($answer) {
                    return $answer;
                }),
                'cert_file'    => $this->askAndValidate('SSL certificate file', [], $path_callback),
                'ca_file'      => $this->askAndValidate('SSL certificate authority file', [], $path_callback),
            ];
        }

        // If there is only 1 locale just use that
        if (count($locales) === 1) {
            $locale = $locales[0];
        } else {
            $default = array_search($this->config->get('app.fallback_locale'), $locales, true);
            $locale  = $this->choice('Language', $locales, $default);
        }

        return [
            'url'      => $url,
            'timezone' => $region,
            'socket'   => $socket,
            'ssl'      => $ssl,
            'locale'   => $locale,
        ];
    }

    /**
     * Prompts the user for the details for the email setup.
     *
     * @return array
     */
    private function getEmailInformation()
    {
        $this->header('Email details');

        $email = [];

        $driver = $this->choice('Type', ['smtp', 'sendmail', 'mail'], 0);

        if ($driver === 'smtp') {
            $host = $this->ask('Host', 'localhost');

            $port = $this->askAndValidate('Port', [], function ($answer) {
                $validator = $this->validator->make(['port' => $answer], [
                    'port' => 'integer',
                ]);

                if (!$validator->passes()) {
                    throw new RuntimeException($validator->errors()->first('port'));
                }

                return $answer;
            }, 25);

            $user = $this->ask('Username');
            $pass = $this->secret('Password');

            $email['host']     = $host;
            $email['port']     = $port;
            $email['username'] = $user;
            $email['password'] = $pass;
        }

        $from_name = $this->ask('From name', 'Deployer');

        $from_address = $this->askAndValidate('From address', [], function ($answer) {
            $validator = $this->validator->make(['from_address' => $answer], [
                'from_address' => 'email',
            ]);

            if (!$validator->passes()) {
                throw new RuntimeException($validator->errors()->first('from_address'));
            }

            return $answer;
        }, 'deployer@deployer.app');

        $email['from_name']    = $from_name;
        $email['from_address'] = $from_address;
        $email['driver']       = $driver;

        return $email;
    }

    /**
     * Prompts for the admin user details.
     *
     * @return array
     */
    private function getAdminInformation()
    {
        $this->header('Admin details');

        $name = $this->ask('Name', 'Admin');

        $email_address = $this->askAndValidate('Email address', [], function ($answer) {
            $validator = $this->validator->make(['email_address' => $answer], [
                'email_address' => 'email',
            ]);

            if (!$validator->passes()) {
                throw new RuntimeException($validator->errors()->first('email_address'));
            }

            return $answer;
        });

        $password = $this->askSecretAndValidate('Password', [], function ($answer) {
            $validator = $this->validator->make(['password' => $answer], [
                'password' => 'min:6',
            ]);

            if (!$validator->passes()) {
                throw new RuntimeException($validator->errors()->first('password'));
            }

            return $answer;
        });

        return [
            'name'     => $name,
            'email'    => $email_address,
            'password' => $password,
        ];
    }

    /**
     * Verifies that the database connection details are correct.
     *
     * @param array $database The connection details
     *
     * @return bool
     */
    private function verifyDatabaseDetails(array $database)
    {
        if ($database['connection'] === 'sqlite') {
            return $this->filesystem->touch(database_path('database.sqlite'));
        }

        try {
            $dsn = $database['connection'] . ':host=' . $database['host'] .
                                             ';port=' . $database['port'] .
                                             ';dbname=' . $database['database'];

            $connection = new PDO(
                $dsn,
                $database['username'],
                $database['password'],
                [
                    PDO::ATTR_PERSISTENT => false,
                    PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT    => 2,
                ]
            );

            unset($connection);

            return true;
        } catch (\Exception $error) {
            $this->failure(
                'Deployer could not connect to the database with the details provided. Please try again.',
                $error->getMessage()
            );
        }

        return false;
    }

    /**
     * Ensures that Deployer has not been installed yet.
     *
     * @return bool
     */
    private function verifyNotInstalled()
    {
        if ($this->config->get('app.key') !== false && $this->config->get('app.key') !== 'SomeRandomString') {
            $this->failure(
                'You have already installed Deployer!',
                'If you were trying to update Deployer, please use "php artisan app:update" instead.'
            );

            return false;
        }

        return true;
    }
}
