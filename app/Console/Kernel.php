<?php

namespace App\Console;

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
        \Illuminate\Foundation\Bootstrap\ConfigureLogging::class => \App\Bootstrap\ConfigureLogging::class,
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
        \App\Console\Commands\CheckHeartbeats::class,
        \App\Console\Commands\CheckUrl::class,
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
