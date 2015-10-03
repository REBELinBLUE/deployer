<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use DateTimeZone;
use Illuminate\Console\Command;
use PDO;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Process\Process;

/**
 * A console command for prompting for install details.
 */
class InstallApp extends Command
{
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->verifyNotInstalled()) {
            return;
        }

        // TODO: Add options so they can be passed in via the command line?

        // FIXME: Handle error!
        // This should not actually be needed as composer install should do it
        if (!file_exists(base_path('.env'))) {
            copy(base_path('.env.example'), base_path('.env'));
        }

        $this->line('');
        $this->info('***********************');
        $this->info('  Welcome to Deployer  ');
        $this->info('***********************');
        $this->line('');

        if (!$this->checkRequirements()) {
            return;
        }

        $this->line('Please answer the following questions:');
        $this->line('');

        $config = [
            'db'    => $this->getDatabaseInformation(),
            'app'   => $this->getInstallInformation(),
            'email' => $this->getEmailInformation(),
        ];

        $this->writeEnvFile($config);
        $this->generateKey();
        $this->migrate();
        $this->optimize();

        $this->line('');
        $this->comment('Success! Deployer is now installed');
        $this->line('');
        $this->comment('Visit ' . $config['app']['url'] . ' and login with the following details to get started');
        $this->line('');
        $this->comment('   Username: admin@example.com');
        $this->comment('   Password: password');
        $this->line('');

        // TODO: Update admin user instead of using defaults?
    }

    /**
     * Writes the configuration data to the config file.
     * 
     * @param  array $input The config data to write
     * @return bool
     */
    private function writeEnvFile(array $input)
    {
        $this->info('Writing configuration file');
        $this->line('');

        $path   = base_path('.env');
        $config = file_get_contents($path);

        // FIXME: Don't use getenv here as it causes a problem if the .env didn't exist, it may not match,
        //  for instance it created a timezone of UTCEurope/London

        // Move the socket value to the correct key
        if (isset($input['app']['socket'])) {
            $input['socket']['url'] = $input['app']['socket'];
            unset($input['app']['socket']);
        }

        foreach ($input as $section => $data) {
            foreach ($data as $key => $value) {
                $env = strtoupper($section . '_' . $key);

                $config = str_replace($env . '=' . getenv($env), $env . '=' . $value, $config);
            }
        }

        if ($input['db']['type'] === 'sqlite') {
            foreach (['host', 'database', 'username', 'password'] as $key) {
                $key = strtoupper($key);

                $config = str_replace('DB_' . $key . '=' . getenv('DB_' . $key) . PHP_EOL, '', $config);
            }
        }

        file_put_contents($path, $config);

        // FIXME: Check the write happened, then make sure rthe file is no longer writeable
    }

    /**
     * Calls the artisan key:generate to set the APP_KEY.
     * 
     * @return void
     */
    private function generateKey()
    {
        $this->info('Generating application key');
        $this->line('');
        $this->call('key:generate');
    }

    /**
     * Calls the artisan migrate to set up the database
     * in development mode it also seeds the DB.
     * 
     * @return void
     */
    private function migrate()
    {
        $this->info('Running database migrations');
        $this->line('');
        $this->call('migrate', ['--force' => true]);

        if (getenv('APP_ENV') === 'local' && getenv('APP_DEBUG') === true) {
            $this->info('Seeding database');
            $this->line('');
            $this->call('db:seed', ['--force' => true]);
        }
    }

    /**
     * Clears all Laravel caches.
     * 
     * @return void
     */
    protected function clearCaches()
    {
        $this->call('clear-compiled');
        $this->call('cache:clear');
        $this->call('route:clear');
        $this->call('config:clear');
        $this->call('view:clear');
    }

    /**
     * Runs the artisan optimize commands.
     * 
     * @return void
     */
    private function optimize()
    {
        $this->clearCaches();

        if (getenv('APP_ENV') !== 'local') {
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

        while (!$connectionVerified) {
            $db = [];

            // Should we just skip this step if only one driver is available?
            $type = $this->choice('Type', $this->getDatabaseDrivers(), 0);

            $db['type'] = $type;

            if ($type !== 'sqlite') {
                $host = $this->ask('Host', 'localhost');
                $name = $this->ask('Name', 'deployer');
                $user = $this->ask('Username', 'deployer');
                $pass = $this->secret('Password');

                $db['host']     = $host;
                $db['name']     = $name;
                $db['username'] = $user;
                $db['password'] = $pass;
            }

            $connectionVerified = $this->verifyDatabaseDetails($db);
        }

        return $db;
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

        $url    = $this->ask('Your Deployer URL ("http://deployer.app" for example)'); // FIXME: Validation
        $region = $this->choice('Your timezone region', array_keys($regions), 0);

        if ($region !== 'UTC') {
            $locations = $this->getTimezoneLocations($regions[$region]);

            $region .= '/' . $this->choice('Your timezone location', $locations, 0);
        }

        $socket = $this->ask('Your socket URL [' . $url . ']', $url); // FIXME: Validation

        // Add APP_LOCALE when we add translations

        return [
            'url'      => $url,
            'timezone' => $region,
            'socket'   => $socket,
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

        // MAIL_DRIVER=smtp
        // MAIL_HOST=mailtrap.io
        // MAIL_PORT=2525
        // MAIL_USERNAME=null
        // MAIL_PASSWORD=null
        // MAIL_FROM_ADDRESS=null
        // MAIL_FROM_NAME=null


        return $email;
    }

    /**
     * Verifies that the database connection details are correct.
     * 
     * @param  array $db The connection details
     * @return bool
     */
    private function verifyDatabaseDetails(array $db)
    {
        if ($db['type'] === 'sqlite') {
            return touch(storage_path() . '/database.sqlite');
        }

        try {
            $pdo = new PDO(
                $db['type'] . ':host=' . $db['host'] . ';dbname=' . $db['name'],
                $db['username'],
                $db['password'],
                [
                    PDO::ATTR_PERSISTENT => false,
                    PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT    => 2,
                ]
            );

            unset($pdo);

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
        // FIXME: Check for valid DB connection, and migrations have run?
        if (getenv('APP_KEY') !== false && getenv('APP_KEY') !== 'SomeRandomString') {
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
    private function checkRequirements()
    {
        $errors = false;

        // Check PHP version:
        if (!version_compare(PHP_VERSION, '5.5.9', '>=')) {
            $this->error('PHP 5.5.9 or higher is required');
            $errors = true;
        }

        // FIXME: GD or imagemagick
        // TODO: See if there are any others, maybe clean this list up?
        $required_extensions = ['PDO', 'curl', 'memcached', 'gd',
                                'mcrypt', 'json', 'tokenizer',
                                'openssl', 'mbstring',
                               ];

        foreach ($required_extensions as $extension) {
            if (!extension_loaded($extension)) {
                $this->error('Extension required: ' . $extension);
                $errors = true;
            }
        }

        if (!count($this->getDatabaseDrivers())) {
            $this->error('At least 1 PDO database driver is required. Either sqlite, mysql, pgsql or sqlsrv, check your php.ini file');
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
        $required_commands = ['ssh', 'ssh-keygen', 'git'];

        foreach ($required_commands as $command) {
            $process = new Process('which ' . $command);

            $process->setTimeout(null);
            $process->run();

            if (!$process->isSuccessful()) {
                $this->error('Program not found in path: ' . $command);
                $errors = true;
            }
        }

        // Files and directories which need to be writable
        $writable = ['.env', 'storage', 'storage/logs', 'storage/app', 'storage/framework',
                     'storage/framework/cache', 'storage/framework/sessions',
                     'storage/framework/views', 'bootstrap/cache',
                    ];

        foreach ($writable as $path) {
            if (!is_writeable(base_path($path))) {
                $this->error($path . ' is not writeable');
                $errors = true;
            }
        }

        // FIXE: Check Memcache and redis are running?

        if ($errors) {
            $this->line('');
            $this->block('Deployer cannot be installed, as not all requirements are met. Please review the errors above before continuing.');
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
        // FIXME: Laravel has collection filtering to make this cleaner
        $available = PDO::getAvailableDrivers();

        $drivers = [];

        foreach (['mysql', 'sqlite', 'pgsql', 'sqlsrv'] as $driver) {
            if (in_array($driver, $available, true)) {
                $drivers[] = $driver;
            }
        }

        return $drivers;
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
     * @param  int   $region The region constant
     * @return array
     * @see DateTimeZone
     */
    private function getTimezoneLocations($region)
    {
        $locations = [];

        foreach (DateTimeZone::listIdentifiers($region) as $timezone) {
            $locations[] = substr($timezone, strlen($region) + 1);
        }

        return $locations;
    }

    /**
     * A wrapper around symfony's formatter helper to output a block.
     * 
     * @param  string|array $messages Messages to output
     * @param  string       $type     The type of message to output
     * @return void
     */
    protected function block($messages, $type = 'error')
    {
        if (!is_array($messages)) {
            $messages = (array) $messages;
        }

        $output = [''];

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
     * @param  string $header The text to output
     * @return void
     */
    protected function header($header)
    {
        $this->block($header, 'question');
    }
}
