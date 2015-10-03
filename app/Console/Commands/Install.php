<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;
use PDO;
use Symfony\Component\Console\Helper\FormatterHelper;

/**
 * A console command for prompting for install details.
 */
class Install extends Command
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

    private function writeEnvFile(array $input)
    {
        $this->info('Writing configuration file');
        $this->line('');

        $path   = base_path('.env');
        $config = file_get_contents($path);

        // FIXME: Don't use getenv here as it causes a problem if the .env didn't exist, it may not match, 
        //  for instance it created a timezone of UTCEurope/London

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
    }

    private function generateKey()
    {
        $this->info('Generating application key');
        $this->line('');
        $this->call('key:generate');
    }

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

    private function getDatabaseInformation()
    {
        $this->header('Database details');

        $connectionVerified = false;

        while (!$connectionVerified) {
            $db = [];

            // FIXME: If only one driver is available just use that!
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

    private function getInstallInformation()
    {
        $this->header('Installation details');
        $regions = [
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

        $install = [];

        $url    = $this->ask('Your Deployer URL ("http://deployer.app" for example)'); // FIXME: Validation
        $region = $this->choice('Your timezone region', array_keys($regions), 0);

        $install['url']      = $url;
        $install['timezone'] = $region;

        if ($region !== 'UTC') {
            $locations = [];

            foreach (DateTimeZone::listIdentifiers($regions[$region]) as $timezone) {
                $locations[] = substr($timezone, strlen($region) + 1);
            }

            $location = $this->choice('Your timezone location', $locations, 0);

            $install['timezone'] .= '/' . $location;
        }

        $socket = $this->ask('Your socket URL [' . $url . ']', $url);

        return $install;
    }

    private function getEmailInformation()
    {
        $this->header('Email details');

        $email = [];

        return $email;
    }

    private function verifyDatabaseDetails(array $db)
    {
        if ($db['type'] === 'sqlite') {
            // FIXME: See if we can get the value from the config
            return touch(storage_path() . '/database.sqlite');
        }

        // FIXME: See if there is a cleaner way to do this in laravel
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

        if (!count($this->getAvailableDrivers())) {
            $this->error('At least 1 database driver is required');
            $errors = true;
        }

        // Functions needed by symfony process
        $required_functions = ['proc_open'];

        foreach ($requiredFunctions as $function) {
            if (!function_exists($function)) {
                $this->error('Function required: ' . $function . '. Is it disabled in php.ini?');
                $errors = true;
            }
        }

        if ($errors) {
            $this->line('');
            $this->block('Deployer cannot be installed, as not all requirements are met. Please review the errors above before continuing.');
            $this->line('');

            return false;
        }

        return true;
    }

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

        return $driver;
    }

    private function block($messages, $type = 'error')
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

    private function header($header)
    {
        $this->block($header, 'question');
    }
}
