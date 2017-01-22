<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use DateTimeZone;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use PDO;
use REBELinBLUE\Deployer\Console\Commands\Traits\AskAndValidate;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * A console command for prompting for install details.
 */
class InstallApp extends Command
{
    use AskAndValidate;

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
    private $config;

    /**
     * InstallApp constructor.
     *
     * @param ConfigRepository $config
     */
    public function __construct(ConfigRepository $config = null)
    {
        parent::__construct();

        $this->config = $config;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->verifyNotInstalled()) {
            return -1;
        }

        $this->clearCaches();

        $config = base_path('.env');

        if (!file_exists($config)) {
            copy(base_path('.env.example'), $config);
            $this->config->set('app.key', 'SomeRandomString');
        }

        $this->line('');
        $this->block(' -- Welcome to Deployer -- ', 'fg=black;bg=green;options=bold');
        $this->line('');

        if (!$this->checkRequirements()) {
            return -1;
        }

        $this->line('Please answer the following questions:');
        $this->line('');

        $config = [
            'db'      => $this->getDatabaseInformation(),
            'app'     => $this->getInstallInformation(),
            'hipchat' => $this->getHipchatInformation(),
            'twilio'  => $this->getTwilioInformation(),
            'mail'    => $this->getEmailInformation(),
        ];

        $admin = $this->getAdminInformation();

        $config['jwt']['secret'] = $this->generateJWTKey();

        $this->writeEnvFile($config);

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
            'Example configuration files can be found in the <options=bold>docs/examples</> directory',
            'Set up your web server, see either <options=bold>nginx.conf</> or <options=bold>apache.conf</>',
            'Setup the cronjobs, see <options=bold>crontab</>',
            'Setup the socket server & queue runner, see <options=bold>supervisor.conf</> for an example setup',
            'Ensure that <options=bold>storage</> and <options=bold>public/upload</> are writable by the webserver',
            'Visit ' . $config['app']['url'] . ' and login with the details you provided to get started',
        ];

        foreach ($instructions as $i => $instruction) {
            if ($i !== 0) {
                $instruction = $i . '. ' . $instruction;
            }

            $this->comment($instruction);
            $this->line('');
        }
    }

    /**
     * Writes the configuration data to the config file.
     *
     * @param array $input The config data to write
     *
     * @return bool
     */
    protected function writeEnvFile(array $input)
    {
        $this->info('Writing configuration file');

        $path   = base_path('.env');
        $config = file_get_contents($path);

        // Move the socket value to the correct key
        if (isset($input['app']['socket'])) {
            $input['socket']['url'] = $input['app']['socket'];
            unset($input['app']['socket']);
        }

        if (isset($input['app']['ssl'])) {
            foreach ($input['app']['ssl'] as $key => $value) {
                $input['socket']['ssl_' . $key] = $value;
            }

            unset($input['app']['ssl']);
        }

        foreach ($input as $section => $data) {
            foreach ($data as $key => $value) {
                $env = strtoupper($section . '_' . $key);

                $config = preg_replace('/' . $env . '=(.*)/', $env . '=' . $value, $config);
            }
        }

        // Remove SSL certificate keys if not using HTTPS
        if (substr($input['socket']['url'], 0, 5) !== 'https') {
            foreach (['key', 'cert', 'ca'] as $key) {
                $key = strtoupper($key);

                $config = preg_replace('/SOCKET_SSL_' . $key . '_FILE=(.*)[\n]/', '', $config);
            }

            $config = preg_replace('/SOCKET_SSL_KEY_PASSPHRASE=(.*)[\n]/', '', $config);
        }

        // Remove keys not needed for sqlite
        if ($input['db']['connection'] === 'sqlite') {
            foreach (['host', 'database', 'username', 'password'] as $key) {
                $key = strtoupper($key);

                $config = preg_replace('/DB_' . $key . '=(.*)[\n]/', '', $config);
            }
        }

        // Remove keys not needed by SMTP
        if ($input['mail']['driver'] !== 'smtp') {
            foreach (['host', 'port', 'username', 'password'] as $key) {
                $key = strtoupper($key);

                $config = preg_replace('/MAIL_' . $key . '=(.*)[\n]/', '', $config);
            }
        }

        // Remove redis password if null
        $config = preg_replace('/REDIS_PASSWORD=null[\n]/', '', $config);

        // Remove github keys if not needed, only really exists on my dev copy
        if (!isset($input['github']) || empty($input['github']['oauth_token'])) {
            $config = preg_replace('/GITHUB_OAUTH_TOKEN=(.*)[\n]/', '', $config);
        }

        // Remove trusted_proxies if not set
        if (!isset($input['trusted']) || !isset($input['trusted']['proxied'])) {
            $config = preg_replace('/TRUSTED_PROXIES=(.*)[\n]/', '', $config);
        }

        // Remove comments
        $config = preg_replace('/#(.*)[\n]/', '', $config);
        $config = preg_replace('/[\n]{3,}/m', PHP_EOL . PHP_EOL, $config);

        return file_put_contents($path, trim($config) . PHP_EOL);
    }

    /**
     * Calls the artisan key:generate to set the APP_KEY.
     */
    private function generateKey()
    {
        $this->info('Generating application key');
        $this->callSilent('key:generate');
    }

    /**
     * Generates a key for JWT.
     *
     * @return string
     */
    protected function generateJWTKey()
    {
        //$this->info('Generating JWT key');
        //$this->callSilent('jwt:generate', ['--force' => true]);

        return str_random(32);
    }

    /**
     * Calls the artisan migrate to set up the database.
     */
    protected function migrate()
    {
        $this->info('Running database migrations');
        $this->line('');
        //$this->call('migrate', ['--force' => true]);

        $builder = new ProcessBuilder;
        $builder->setPrefix('php');

        // Something has changed in laravel 5.3 which means calling the migrate command with call() isn't working
        $process = $builder->setArguments([
            base_path('artisan'), 'migrate', '--force',
        ])->setWorkingDirectory(base_path('artisan'))
          ->getProcess()
          ->setTty(true)
          ->setTimeout(null);

        $process->run(function ($type, $buffer) {
            if ($type === Process::OUT) {
                echo $buffer;
            }
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process);
        }

        $this->line('');
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
        $builder = new ProcessBuilder;
        $builder->setPrefix('php');

        $process = $builder->setArguments([
            base_path('artisan'), 'deployer:create-user', $name, $email, $password,
        ])->setWorkingDirectory(base_path('artisan'))
          ->getProcess()
          ->setTimeout(null);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process);
        }
    }

    /**
     * Clears all Laravel caches.
     * @param bool $silent
     */
    protected function clearCaches($silent = true)
    {
        $this->callCommand('clear-compiled', [], $silent);
        $this->callCommand('cache:clear', [], $silent);
        $this->callCommand('route:clear', [], $silent);
        $this->callCommand('config:clear', [], $silent);
        $this->callCommand('view:clear', [], $silent);
    }

    /**
     * Runs the artisan optimize commands.
     */
    protected function optimize()
    {
        if ($this->getLaravel()->environment() !== 'local') {
            $this->call('optimize', ['--force' => true]);
            $this->call('config:cache');
            $this->call('route:cache');
        }
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

            $connectionVerified = $this->verifyDatabaseDetails($database);
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
        $locales = $this->getLocales();

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
            $process = new Process('which nginx');
            $process->setTimeout(null);
            $process->run();

            if (!$process->isSuccessful()) {
                $socket .= ':6001';
            }
        }

        $path_callback = function ($answer) {
            $validator = Validator::make(['path' => $answer], [
                'path' => 'required',
            ]);

            if (!$validator->passes()) {
                throw new \RuntimeException($validator->errors()->first('path'));
            }

            if (!file_exists($answer)) {
                throw new \RuntimeException('File does not exist');
            }

            return $answer;
        };

        $ssl = null;
        if (substr($socket, 0, 5) === 'https') {
            $ssl = [
                'key_file'     => $this->askAndValidate('SSL key File', [], $path_callback),
                'key_password' => $this->secret('SSL key password'),
                'cert_file'    => $this->askAndValidate('SSL certificate File', [], $path_callback),
                'ca_file'      => $this->askAndValidate('SSL certificate authority file', [], $path_callback),
            ];
        };

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
                $validator = Validator::make(['port' => $answer], [
                    'port' => 'integer',
                ]);

                if (!$validator->passes()) {
                    throw new \RuntimeException($validator->errors()->first('port'));
                };

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
            $validator = Validator::make(['from_address' => $answer], [
                'from_address' => 'email',
            ]);

            if (!$validator->passes()) {
                throw new \RuntimeException($validator->errors()->first('from_address'));
            };

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
            $validator = Validator::make(['email_address' => $answer], [
                'email_address' => 'email',
            ]);

            if (!$validator->passes()) {
                throw new \RuntimeException($validator->errors()->first('email_address'));
            };

            return $answer;
        });

        $password = $this->askSecretAndValidate('Password', [], function ($answer) {
            $validator = Validator::make(['password' => $answer], [
                'password' => 'min:6',
            ]);

            if (!$validator->passes()) {
                throw new \RuntimeException($validator->errors()->first('password'));
            };

            return $answer;
        });

        return [
            'name'     => $name,
            'email'    => $email_address,
            'password' => $password,
        ];
    }

    /**
     * Prompts for the twilio API details.
     *
     * @return array
     */
    public function getTwilioInformation()
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
    public function getHipchatInformation()
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
     * Verifies that the database connection details are correct.
     *
     * @param array $database The connection details
     *
     * @return bool
     */
    private function verifyDatabaseDetails(array $database)
    {
        if ($database['connection'] === 'sqlite') {
            return touch(database_path('database.sqlite'));
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
            $this->block([
                'Deployer could not connect to the database with the details provided. Please try again.',
                PHP_EOL,
                $error->getMessage(),
            ]);
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
        if (config('app.key') !== false && config('app.key') !== 'SomeRandomString') {
            $this->block([
                'You have already installed Deployer!',
                PHP_EOL,
                'If you were trying to update Deployer, please use "php artisan app:update" instead.',
            ]);

            return false;
        }

        return true;
    }

    /**
     * Checks the system meets all the requirements needed to run Deployer.
     *
     * @return bool
     */
    protected function checkRequirements()
    {
        $errors = false;

        // Check PHP version:
        if (!version_compare(PHP_VERSION, '5.6.4', '>=')) {
            $this->error('PHP 5.6.4 or higher is required');
            $errors = true;
        }

        // Check for required PHP extensions
        $required_extensions = ['PDO', 'curl', 'gd', 'json',
                                'tokenizer', 'openssl', 'mbstring',
                               ];

        foreach ($required_extensions as $extension) {
            if (!extension_loaded($extension)) {
                $this->error('Extension required: ' . $extension);
                $errors = true;
            }
        }

        if (!count($this->getDatabaseDrivers())) {
            $this->error(
                'At least 1 PDO driver is required. Either sqlite, mysql or pgsql, check your php.ini file'
            );
            $errors = true;
        }

        // Functions needed by symfony process
        $required_functions = ['proc_open'];

        foreach ($required_functions as $function) {
            if (!function_exists($function)) {
                $this->error('Function required: ' . $function . '. Is it disabled in php.ini?');
                $errors = true;
            }
        }

        // Programs needed in $PATH
        $required_commands = ['ssh', 'ssh-keygen', 'git', 'scp', 'tar', 'gzip', 'rsync', 'bash', 'php'];

        foreach ($required_commands as $command) {
            $process = new Process('which ' . $command);
            $process->setTimeout(null);
            $process->run();

            if (!$process->isSuccessful()) {
                $this->error('Program not found in path: ' . $command);
                $errors = true;
            }
        }

        $required_one = ['node', 'nodejs'];
        $found        = false;
        foreach ($required_one as $command) {
            $process = new Process('which ' . $command);
            $process->setTimeout(null);
            $process->run();

            if ($process->isSuccessful()) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->error('node.js was not found');
            $errors = true;
        }

        // Files and directories which need to be writable
        $writable = ['.env', 'storage', 'storage/logs', 'storage/app', 'storage/app/mirrors', 'storage/app/tmp',
                     'storage/framework', 'storage/framework/cache', 'storage/framework/sessions',
                     'storage/framework/views', 'bootstrap/cache', 'public/upload',
                    ];

        foreach ($writable as $path) {
            if (!is_writeable(base_path($path))) {
                $this->error($path . ' is not writable');
                $errors = true;
            }
        }

        // Check that redis is running
        try {
            Redis::connection()->ping();
        } catch (\Exception $e) {
            $this->error('Redis is not running');
            $errors = true;
        }

        if (config('queue.default') === 'beanstalkd') {
            $connected = Queue::connection()->getPheanstalk()
                                            ->getConnection()
                                            ->isServiceListening();

            if (!$connected) {
                $this->error('Beanstalkd is not running');
                $errors = true;
            }
        }

        if ($errors) {
            $this->line('');
            $this->block('Deployer cannot be installed. Please review the errors above before continuing.');
            $this->line('');

            return false;
        }

        return true;
    }

    /**
     * Gets an array of available PDO drivers which are supported by Laravel.
     *
     * @return array
     */
    private function getDatabaseDrivers()
    {
        $available = collect(PDO::getAvailableDrivers());

        return array_values($available->intersect(['mysql', 'pgsql', 'sqlite'])->all());
    }

    /**
     * Gets a list of timezone regions.
     *
     * @return array
     */
    private function getTimezoneRegions()
    {
        return [
            'UTC'        => DateTimeZone::UTC,
            'Africa'     => DateTimeZone::AFRICA,
            'America'    => DateTimeZone::AMERICA,
            'Antarctica' => DateTimeZone::ANTARCTICA,
            'Asia'       => DateTimeZone::ASIA,
            'Atlantic'   => DateTimeZone::ATLANTIC,
            'Australia'  => DateTimeZone::AUSTRALIA,
            'Europe'     => DateTimeZone::EUROPE,
            'Indian'     => DateTimeZone::INDIAN,
            'Pacific'    => DateTimeZone::PACIFIC,
        ];
    }

    /**
     * Gets a list of available locations in the supplied region.
     *
     * @param int $region The region constant
     *
     * @return array
     *
     * @see DateTimeZone
     */
    private function getTimezoneLocations($region)
    {
        $locations = [];

        foreach (DateTimeZone::listIdentifiers($region) as $timezone) {
            $locations[] = substr($timezone, strpos($timezone, '/') + 1);
        }

        return $locations;
    }

    /**
     * Gets a list of the available locales.
     *
     * @return array
     */
    private function getLocales()
    {
        $manager = resolve('locale');

        return $manager->getAvailableLanguages();
    }

    /**
     * A wrapper around symfony's formatter helper to output a block.
     *
     * @param string|array $messages Messages to output
     * @param string       $type     The type of message to output
     */
    protected function block($messages, $type = 'error')
    {
        $output = [];

        if (!is_array($messages)) {
            $messages = (array) $messages;
        }

        $output[] = '';

        foreach ($messages as $message) {
            $output[] = trim($message);
        }

        $output[] = '';

        $formatter = new FormatterHelper();
        $this->line($formatter->formatBlock($output, $type));
    }

    /**
     * Outputs a header block.
     *
     * @param string $header The text to output
     */
    protected function header($header)
    {
        $this->block($header, 'question');
    }

    /**
     * Calls an artisan command and optionally silences the output.
     *
     * @param string $command
     * @param array  $arguments
     * @param bool   $silent
     */
    protected function callCommand($command, array $arguments = [], $silent = false)
    {
        if ($silent) {
            $this->callSilent($command, $arguments);

            return;
        }

        $this->call($command, $arguments);
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
        $validator = Validator::make(['url' => $answer], [
            'url' => 'url',
        ]);

        if (!$validator->passes()) {
            throw new \RuntimeException($validator->errors()->first('url'));
        }

        return preg_replace('#/$#', '', $answer);
    }
}
