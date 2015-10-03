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
        if (!$this->verifyInstalled()) {
            return;
        }

        // Check for no running deployments
        // Take offline
        // Check for differences in config?
        // Make sure composer install has been run?
        // Migrate
        // Clear cache
        // Optimize
        // Take live
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