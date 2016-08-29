<?php

namespace REBELinBLUE\Deployer\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Bootstrap\ConfigureLogging;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use REBELinBLUE\Deployer\Bootstrap\ConfigureLogging as ConsoleLogging;
use REBELinBLUE\Deployer\Console\Commands\CheckHeartbeats;
use REBELinBLUE\Deployer\Console\Commands\CheckUrl;
use REBELinBLUE\Deployer\Console\Commands\ClearOldKeys;
use REBELinBLUE\Deployer\Console\Commands\ClearOrphanAvatars;
use REBELinBLUE\Deployer\Console\Commands\ClearOrphanMirrors;
use REBELinBLUE\Deployer\Console\Commands\ClearStalledDeployment;
use REBELinBLUE\Deployer\Console\Commands\CreateUser;
use REBELinBLUE\Deployer\Console\Commands\InstallApp;
use REBELinBLUE\Deployer\Console\Commands\ResetApp;
use REBELinBLUE\Deployer\Console\Commands\UpdateApp;
use REBELinBLUE\Deployer\Console\Commands\UpdateGitMirrors;

/**
 * Kernel class.
 */
class Kernel extends ConsoleKernel
{
    /**
     * The custom bootstrappers like Logging or Environment detector.
     *
     * @var array
     */
    protected $customBooters = [
        ConfigureLogging::class => ConsoleLogging::class,
    ];

    /**
     * Disable bootstrapper list.
     *
     * @var array
     */
    protected $disabledBooters = [

    ];

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CheckHeartbeats::class,
        CheckUrl::class,
        CreateUser::class,
        ClearOrphanAvatars::class,
        ClearOrphanMirrors::class,
        ClearStalledDeployment::class,
        ClearOldKeys::class,
        UpdateGitMirrors::class,
        InstallApp::class,
        UpdateApp::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('deployer:heartbeats')
                 ->everyFiveMinutes()
                 ->withoutOverlapping();

        $schedule->command('deployer:update-mirrors')
                 ->everyFiveMinutes()
                 ->withoutOverlapping();

        $schedule->command('deployer:checkurls')
                 ->everyFiveMinutes()
                 ->withoutOverlapping();

        $schedule->command('deployer:purge-avatars')
                 ->weekly()
                 ->sundays()
                 ->at('00:30')
                 ->withoutOverlapping();

        $schedule->command('deployer:purge-mirrors')
                 ->daily()
                 ->withoutOverlapping();

        $schedule->command('deployer:purge-temp')
                 ->hourly()
                 ->withoutOverlapping();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }

    /**
     * Bootstrap the application for artisan commands.
     */
    public function bootstrap()
    {
        parent::bootstrap();

        // Only register the reset command on the local environment
        if ($this->app->environment() === 'local') {
            $this->commands[] = ResetApp::class;
        }
    }

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        foreach ($this->bootstrappers as &$bootstrapper) {
            foreach ($this->customBooters as $sourceBooter => $newBooter) {
                if ($bootstrapper === $sourceBooter) {
                    $bootstrapper = $newBooter;
                    unset($this->customBooters[$sourceBooter]);
                }
            }
        }

        return array_merge(
            array_diff(
                $this->bootstrappers,
                $this->disabledBooters
            ),
            $this->customBooters
        );
    }
}
