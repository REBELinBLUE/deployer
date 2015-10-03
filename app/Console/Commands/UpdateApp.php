<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;

/**
 * A console command for updating the installation.
 */
class UpdateApp extends InstallApp
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes any updates needed for the application.';

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
        if (!$this->verifyInstalled() || $this->hasRunningDeployments()) {
            return;
        }


        // Check if the composer autoload.php has been updated in the last 10 minutes
        if (filemtime(base_path('vendor/autoload.php')) + 600 < time()) {
            $this->block([
                'Update not complete!',
                PHP_EOL,
                'Please run "composer install" before you continue.',
            ]);

            return;
        }

        $this->call('down');

        $this->updateConfiguration();

        $this->migrate();
        $this->optimize();
        $this->restartQueue();

        $this->call('up');
    }

    /**
     * Checks if there are any running or pending deployments.
     * 
     * @return boolean
     */
    protected function hasRunningDeployments()
    {
        //$this->error('There are still running deployments, please wait for them to finish before updating');

        return false;
    }

    /**
     * Checks for new configuration values in .env.example and copy them to .env.
     * 
     * @return void
     */
    protected function updateConfiguration()
    {

    }

    /**
     * Restarts the queues.
     * 
     * @return void
     */
    protected function restartQueue()
    {
        $this->info('Restarting the queue');
        $this->line('');
        $this->call('queue:flush');
        $this->call('queue:restart');
        $this->line('');
    }

    /**
     * Ensures that Deployer has actually been installed.
     * 
     * @return bool
     */
    private function verifyInstalled()
    {
        if (getenv('APP_KEY') === false || getenv('APP_KEY') === 'SomeRandomString') {
            $this->block([
                'Deployer has not been installed',
                PHP_EOL,
                'Please use "php artisan app:install" instead.',
            ]);

            return false;
        }

        return true;
    }
}
