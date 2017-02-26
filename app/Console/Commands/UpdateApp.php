<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Carbon\Carbon;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use REBELinBLUE\Deployer\Console\Commands\Installer\EnvFile;
use REBELinBLUE\Deployer\Console\Commands\Installer\Requirements;
use REBELinBLUE\Deployer\Console\Commands\Traits\OutputStyles;
use REBELinBLUE\Deployer\Events\RestartSocketServer;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;

/**
 * A console command for updating the installation.
 */
class UpdateApp extends Command
{
    use OutputStyles;

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
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ConfigRepository
     */
    private $config;

    /**
     * @var DeploymentRepositoryInterface
     */
    private $repository;

    /**
     * @var Requirements
     */
    private $requirements;

    /**
     * @var EnvFile
     */
    private $env;

    /**
     * UpdateApp constructor.
     *
     * @param ConfigRepository              $config
     * @param Filesystem                    $filesystem
     * @param DeploymentRepositoryInterface $repository
     * @param Requirements                  $requirements
     * @param EnvFile                       $writer
     */
    public function __construct(
        ConfigRepository $config,
        Filesystem $filesystem,
        DeploymentRepositoryInterface $repository,
        Requirements $requirements,
        EnvFile $writer
    ) {
        parent::__construct();

        $this->config       = $config;
        $this->filesystem   = $filesystem;
        $this->repository   = $repository;
        $this->requirements = $requirements;
        $this->env          = $writer;
    }

    /**
     * Execute the console command.
     *
     * @param  Dispatcher $dispatcher
     * @return int
     */
    public function handle(Dispatcher $dispatcher)
    {
        $this->line('');

        if (!$this->checkCanInstall()) {
            return -1;
        }

        $bring_back_up = false;

        if (!$this->laravel->isDownForMaintenance()) {
            $this->error(
                'You must switch to maintenance mode before running this command, ' .
                'this will ensure that no new deployments are started'
            );

            if (!$this->confirm(
                'Switch to maintenance mode now? The app will switch ' .
                'back to live mode once cleanup is finished'
            )) {
                return -1;
            }

            $bring_back_up = true;

            $this->call('down');
        }

        $this->backupDatabase();
        $this->updateConfiguration();
        $this->clearCaches();
        $this->migrate();
        $this->optimize();
        $this->restartQueue();
        $this->restartSocket($dispatcher);

        // If we prompted the user to bring the app down, bring it back up
        if ($bring_back_up) {
            $this->call('up');
        }

        return 0;
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
            $this->call('optimize', ['--force' => true]);
            $this->call('config:cache');
            $this->call('route:cache');
        }
    }

    /**
     * Calls the artisan migrate command.
     */
    protected function migrate()
    {
        $this->info('Running database migrations');
        $this->line('');
        $this->call('migrate', ['--force' => true]);
    }

    /**
     * Backup the database.
     */
    protected function backupDatabase()
    {
        $date = Carbon::now()->format('Y-m-d H.i.s');

        $this->call('db:backup', [
            '--database'        => $this->config->get('database.default'),
            '--destination'     => 'local',
            '--destinationPath' => $date,
            '--compression'     => 'gzip',
        ]);
    }

    /**
     * Checks for new configuration values in .env.dist and copy them to .env.
     */
    protected function updateConfiguration()
    {
        // Write the file to disk
        $this->info('Updating configuration file');
        $this->env->update();
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
     * Checks if there are any running or pending deployments.
     *
     * @return bool
     */
    protected function hasRunningDeployments()
    {
        $running = $this->repository->getRunning()->count();
        $pending = $this->repository->getPending()->count();

        if ($running > 0 || $pending > 0) {
            $this->failure(
                'Deployments in progress',
                'There are still running deployments, please wait for them to finish before updating.'
            );

            return true;
        }

        return false;
    }

    /**
     * Check if the composer autoload.php has been updated in the last 10 minutes,
     * if not we assume composer install has not be run recently.
     *
     * @return bool
     */
    protected function composerOutdated()
    {
        $timestamp = Carbon::now()->subMinutes(10)->timestamp;
        if ($this->filesystem->lastModified(base_path('vendor/autoload.php')) < $timestamp) {
            $this->failure(
                'Update not complete!',
                'Please run "composer install --no-suggest --no-dev -o" before you continue.'
            );

            return true;
        }

        return false;
    }

    /**
     * Check if the a .install file in the node_modules folder has been updated in the last 10 minutes,
     * if not we assume npm install has not been run recently as it is touched by "postinstall".
     *
     * @return bool
     */
    protected function nodeOutdated()
    {
        $timestamp = Carbon::now()->subMinutes(10)->timestamp;
        if ($this->filesystem->lastModified(base_path('node_modules/.install')) < $timestamp) {
            $this->failure('Update not complete!', 'Please run "npm install --production" before you continue.');

            return true;
        }

        return false;
    }

    /**
     * Runs all the checks for whether the updater can be run.
     *
     * @return bool
     */
    private function checkCanInstall()
    {
        return (
            $this->verifyInstalled() &&
            !$this->hasDeprecatedConfig() &&
            !$this->composerOutdated() &&
            !$this->nodeOutdated() &&
            !$this->hasRunningDeployments() &&
            $this->requirements->check($this)
        );
    }

    /**
     * Ensures that Deployer has actually been installed.
     *
     * @return bool
     */
    private function verifyInstalled()
    {
        if ($this->config->get('app.key') === false || $this->config->get('app.key') === 'SomeRandomString') {
            $this->failure('Deployer has not been installed', 'Please use "php artisan app:install" instead.');

            return false;
        }

        return true;
    }

    /**
     * Ensures the config file has been updated.
     *
     * @return bool
     */
    private function hasDeprecatedConfig()
    {
        if (preg_match('/DB_TYPE=/', $this->filesystem->get(base_path('.env')))) {
            $this->failure(
                'Update not complete!',
                'Your .env file has a DB_TYPE key, please rename this to DB_CONNECTION and try again'
            );

            return true;
        }

        return false;
    }
}
