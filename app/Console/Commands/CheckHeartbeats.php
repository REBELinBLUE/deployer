<?php namespace App\Console\Commands;

use Carbon\Carbon;
use App\Heartbeat;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Checks that any expected heartbeats have checked-in
 */
class CheckHeartbeats extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'heartbeat:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks that any expected heartbeats have checked-in';

    /**
     * Checks that heartbeats happened as expected
     *
     * @return mixed
     */
    public function fire()
    {
        $heartbeats = Heartbeat::where('status', '=', Heartbeat::OK);

        foreach ($heartbeats as $heartbeat)
        {
            $last_heard_from = $heartbeat->last_activity;
            if ($heartbeat->status === Heartbeat::UNTESTED) {
                $last_heard_from = $heartbeat->created_at;
            }

            $next_time = $last_heard_from->addMinutes($heartbeat->interval);

            if (Carbon::now()->gt($next_time)) {
                $heartbeat->status = Heartbeat::MISSING;
                $heartbeat->save();
            }
        }
    }
}
