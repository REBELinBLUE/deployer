<?php namespace App\Console\Commands;

use Queue;
use Carbon\Carbon;
use App\Heartbeat;
use App\Commands\Notify;
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
        $heartbeats = Heartbeat::all();

        foreach ($heartbeats as $heartbeat) {
            $last_heard_from = $heartbeat->last_activity;
            if (!$last_heard_from) {
                $last_heard_from = $heartbeat->created_at;
            }

            $missed = $heartbeat->missed + 1;

            $next_time = $last_heard_from->addMinutes($heartbeat->interval * $missed);

            if (Carbon::now()->gt($next_time)) {
                $heartbeat->status = Heartbeat::MISSING;
                $heartbeat->missed = $missed;
                $heartbeat->save();

                foreach ($heartbeat->project->notifications as $notification) {
                    Queue::pushOn('notify', new Notify($notification, $heartbeat->notificationPayload()));
                }
            }
        }
    }
}
