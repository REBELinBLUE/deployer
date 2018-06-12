<?php

namespace REBELinBLUE\Deployer\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use JsLocalization\Console\ExportCommand;
use REBELinBLUE\Deployer\Console\Commands\AppVersion;
use REBELinBLUE\Deployer\Console\Commands\CheckHeartbeats;
use REBELinBLUE\Deployer\Console\Commands\CheckUrls;
use REBELinBLUE\Deployer\Console\Commands\ClearOldKeys;
use REBELinBLUE\Deployer\Console\Commands\ClearOrphanAvatars;
use REBELinBLUE\Deployer\Console\Commands\ClearOrphanMirrors;
use REBELinBLUE\Deployer\Console\Commands\ClearStalledDeployment;
use REBELinBLUE\Deployer\Console\Commands\CreateUser;
use REBELinBLUE\Deployer\Console\Commands\DebugApp;
use REBELinBLUE\Deployer\Console\Commands\InstallApp;
use REBELinBLUE\Deployer\Console\Commands\MakeRepositoryCommand;
use REBELinBLUE\Deployer\Console\Commands\ResetApp;
use REBELinBLUE\Deployer\Console\Commands\UpdateApp;
use REBELinBLUE\Deployer\Console\Commands\UpdateGitMirrors;

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
        DebugApp::class,
        UpdateGitMirrors::class,
        InstallApp::class,
        UpdateApp::class,
        AppVersion::class,
        ExportCommand::class,
        MakeRepositoryCommand::class,
    ];

    /**
     * Bootstrap the application for artisan commands.
     */
    public function bootstrap()
    {
        parent::bootstrap();

        // Only register the reset command on the local environment
        if ($this->app->environment('local', 'testing')) {
            $this->commands[] = ResetApp::class;
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
//
//    /**
//     * Register the commands for the application.
//     *
//     * @return void
//     */
//    protected function commands()
//    {
//        $this->load(__DIR__.'/Commands');
//        require base_path('routes/console.php');
//    }
}
