<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles;
use REBELinBLUE\Deployer\Events\RestartSocketServer;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;

/**
 * A console command for clearing all data and setting up again.
 */
class ResetApp extends Command
{
    use OutputStyles;

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
     * @var Filesystem
     */
    private $filesystem;

    /**
     * ResetApp constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @param  Dispatcher $dispatcher
     * @return int
     */
    public function handle(Dispatcher $dispatcher)
    {
        if (!$this->verifyNotProduction()) {
            return -1;
        }

        $this->callSilent('down');

        $this->resetDatabase();
        $this->clearLogs();
        $this->restartQueue();
        $this->restartSocket($dispatcher);

        $this->callSilent('up');

        return 0;
    }

    /**
     * Resets the database.
     */
    protected function resetDatabase()
    {
        $this->filesystem->touch(base_path('vendor/autoload.php'));
        $this->filesystem->touch(base_path('node_modules/.install'));

        $this->callSilent('migrate', ['--force' => true]);
        $this->callSilent('app:update', ['--no-backup' => true]);
        $this->call('migrate:refresh', ['--seed' => true, '--force' => true]);
    }

    /**
     * Restarts the queues.
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
     * Restarts the socket server.
     *
     * @param Dispatcher $dispatcher
     */
    protected function restartSocket(Dispatcher $dispatcher)
    {
        $this->info('Restarting the socket server');
        $dispatcher->dispatch(new RestartSocketServer());
    }

    /**
     * Removes the log files.
     */
    protected function clearLogs()
    {
        $this->info('Removing log files');
        $this->line('');

        $logs = $this->filesystem->glob(storage_path('logs') . '/*.log');
        $this->filesystem->delete($logs);
    }

    /**
     * Ensures that the command is running locally and in debugging mode.
     *
     * @return bool
     */
    private function verifyNotProduction()
    {
        if (!$this->laravel->environment('local')) {
            $this->failure(
                'Deployer is not in development mode!',
                'This command does not run in production as its purpose is to wipe your database'
            );

            return false;
        }

        return true;
    }
}
