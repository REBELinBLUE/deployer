<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use REBELinBLUE\Deployer\Events\HeartbeatMissed;
use REBELinBLUE\Deployer\Heartbeat;

/**
 * Checks that any expected heartbeats have checked-in.
 */
class CheckHeartbeats extends Command
{
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
     * Execute the console command.
     *
     * @fires HeartbeatMissed
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

                    event(new HeartbeatMissed($heartbeat));
                }
            }
        });
    }
}
