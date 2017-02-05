<?php

namespace REBELinBLUE\Deployer\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use REBELinBLUE\Deployer\Console\Commands\AppVersion;
use REBELinBLUE\Deployer\Console\Commands\CheckHeartbeats;
use REBELinBLUE\Deployer\Console\Commands\CheckUrls;
use REBELinBLUE\Deployer\Console\Commands\ClearOldKeys;
use REBELinBLUE\Deployer\Console\Commands\ClearOrphanAvatars;
use REBELinBLUE\Deployer\Console\Commands\ClearOrphanMirrors;
use REBELinBLUE\Deployer\Console\Commands\ClearStalledDeployment;
use REBELinBLUE\Deployer\Console\Commands\CreateUser;
use REBELinBLUE\Deployer\Console\Commands\InstallApp;
use REBELinBLUE\Deployer\Console\Commands\ResetApp;
use REBELinBLUE\Deployer\Console\Commands\UpdateApp;
use REBELinBLUE\Deployer\Console\Commands\UpdateGitMirrors;
use Spatie\MigrateFresh\Commands\MigrateFresh;

/**
 * Kernel class.
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CheckHeartbeats::class,
        CheckUrls::class,
        CreateUser::class,
        ClearOrphanAvatars::class,
        ClearOrphanMirrors::class,
        ClearStalledDeployment::class,
        ClearOldKeys::class,
        UpdateGitMirrors::class,
        InstallApp::class,
        UpdateApp::class,
        AppVersion::class,
    ];

    /**
     * Bootstrap the application for artisan commands.
     */
    public function bootstrap()
    {
        parent::bootstrap();

        // Only register the reset command on the local environment when dev dependencies are installed
        if ($this->app->environment() === 'local' && class_exists(MigrateFresh::class, true)) {
            $this->commands[] = ResetApp::class;
            $this->commands[] = MigrateFresh::class;
        }
    }

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
}
