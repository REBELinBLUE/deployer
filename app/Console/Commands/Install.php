<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use DateTime;
use DateTimeZone;
use PDO;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\FormatterHelper;

/**
 * A console command for prompting for install details
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

        // TODO: Check requirements
        
        $this->line('Please answer the following questions:');
        $this->line('');

        $config = [
            'db'    => $this->getDatabaseInformation(),
            'app'   => $this->getInstallInformation(),
            'email' => $this->getEmailInformation()
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

    private function writeEnvFile($config)
    {
        $this->info('Writing configuration file');
        $this->line('');
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
        //$this->call('migrate', ['--force' => true]);
        //
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
            $db = array();

            // FIXME: Check for installed drivers!
            $type = $this->choice('Type [mysql]', ['mysql', 'sqlite', 'pgsql'], 0);

            $db['type'] = $type;

            if ($type !== 'sqlite') {
                $host = $this->ask('Host', 'localhost');
                $name = $this->ask('Name', 'deployer');
                $user = $this->ask('Username', 'deployer');
                $pass = $this->secret('Password');

                $db['host'] = $host;
                $db['name'] = $name;
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

        $install = array();

        $url = $this->ask('Your Deployer URL ("http://deployer.app" for example)'); // FIXME: Validation
        $region = $this->choice('Your timezone region', array_keys($regions), 0);

        $install['url'] = $url;
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

        $email = array();

        return $email;
    }

    private function verifyDatabaseDetails(array $db)
    {
        if ($db['type'] === 'sqlite') {
            // FIXME: Touch the DB and check it exists
            return true;
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
                $error->getMessage()
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

    private function block($messages, $type = 'error')
    {
        if (!is_array($messages)) {
            $messages = (array) $messages;
        }

        $output = array('');

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



    // protected function writeEnv($key, $value)
    // {
    //     static $path = null;
    //     if ($path === null || ($path !== null && file_exists($path))) {
    //         $path = base_path('.env');
    //         file_put_contents($path, str_replace(
    //             getenv(strtoupper($key)), $value, file_get_contents($path)
    //         ));
    //     }
    // }
