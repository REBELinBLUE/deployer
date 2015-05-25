<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Kernel class
 */
class Kernel extends ConsoleKernel
{

    /**
     * The custom bootstrappers like Logging or Environment detector
     * @var array
     */
    protected $customBooters = [
        'App\Bootstrap\ConfigureLogging',
    ];

    /**
     * Disable bootstrapper list
     * @var array
     */
    protected $disabledBooters = [
        'Illuminate\Foundation\Bootstrap\ConfigureLogging',
    ];

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\CheckHeartbeats'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('heartbeat:check')
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
        return array_merge(
            array_diff(
                $this->bootstrappers,
                $this->disabledBooters
            ),
            $this->customBooters
        );
    }
}
