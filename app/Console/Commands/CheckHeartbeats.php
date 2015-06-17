<?php

namespace App\Console\Commands;

use App\Heartbeat;
use App\Jobs\Notify;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Checks that any expected heartbeats have checked-in.
 */
class CheckHeartbeats extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployer:heartbeats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks that any expected heartbeats have checked-in';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Heartbeat::chunk(10, function ($heartbeats) {
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
                        $this->dispatch(new Notify(
                            $notification,
                            $heartbeat->notificationPayload()
                        ));
                    }
                }
            }

        });
    }
}
