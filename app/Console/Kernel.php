<?php

namespace REBELinBLUE\Deployer\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Kernel class.
 */
class Kernel extends ConsoleKernel
{
    /**
     * The custom bootstrappers like Logging or Environment detector.
     * @var array
     */
    protected $customBooters = [
        \Illuminate\Foundation\Bootstrap\ConfigureLogging::class
            => \REBELinBLUE\Deployer\Bootstrap\ConfigureLogging::class,
    ];

    /**
     * Disable bootstrapper list.
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
        \REBELinBLUE\Deployer\Console\Commands\CheckHeartbeats::class,
        \REBELinBLUE\Deployer\Console\Commands\CheckUrl::class,
        \REBELinBLUE\Deployer\Console\Commands\ClearOrphanAvatars::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('deployer:heartbeats')
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
