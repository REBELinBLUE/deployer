<?php

namespace REBELinBLUE\Deployer\Console\Commands;

/**
 * A console command for clearing all data and setting up again.
 * @property \Illuminate\Contracts\Foundation\Application app
 */
class ResetApp extends UpdateApp
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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->verifyNotProduction()) {
            return;
        }

        $this->app = $this->laravel;

        $this->clearLogs();
        $this->updateConfiguration();
        $this->resetDB();
        $this->migrate();
        $this->seedDB();
        $this->clearCaches();
        $this->restartQueue();
        $this->restartSocket();
    }

    /**
     * Resets the database.
     */
    protected function resetDB()
    {
        $this->info('Resetting the database');
        $this->line('');
        $this->call('migrate:reset', ['--force' => true]);
        $this->line('');
    }

    /**
     * Removes the log files.
     */
    protected function clearLogs()
    {
        $this->info('Removing log files');
        $this->line('');

        foreach (glob(storage_path('logs/') . '*.log*') as $file) {
            unlink($file);
        }
    }

    /**
     * Ensures that the command is running locally and in debugging mode.
     *
     * @return bool
     */
    private function verifyNotProduction()
    {
        if (config('app.env') !== 'local') {
            $this->block([
                'Deployer is not in development mode!',
                PHP_EOL,
                'This command does not run in production as its purpose is to wipe your database',
            ]);

            return false;
        }

        return true;
    }

    /**
     * Seeds the database
     *
     * @return void
     */
    private function seedDB()
    {
        $this->info('Seeding database');
        $this->line('');
        $this->call('db:seed', ['--force' => true]);
        $this->line('');
    }
}
