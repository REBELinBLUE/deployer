<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;

/**
 * A console command for clearing all data and setting up again.
 */
class ResetApp extends InstallApp
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used during development to clear the database and logs';

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
        if (!$this->verifyNotProduction()) {
            return;
        }

        $this->resetDB();
        $this->migrate();
        $this->optimize();
    }

    protected function resetDB()
    {
        $this->info('Resetting the database');
        $this->line('');
        $this->call('migrate:reset', ['--force' => true]);
        $this->line('');
    }

    /**
     * Ensures that the command is running locally and in debugging mode.
     * 
     * @return boolean
     */
    private function verifyNotProduction()
    {
        if ($this->getLaravel()->environment() !== 'local') {
            $this->block([
                'Deployer is not in development mode!',
                PHP_EOL,
                'This command does not run in production as its purpose is to wipe your database'
            ]);

            return false;
        }

        return true;
    }
}