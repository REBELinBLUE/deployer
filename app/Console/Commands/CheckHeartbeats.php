<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Events\HeartbeatMissed;
use REBELinBLUE\Deployer\Heartbeat;
use REBELinBLUE\Deployer\Repositories\Contracts\HeartbeatRepositoryInterface;

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
     * @var HeartbeatRepositoryInterface
     */
    private $repository;

    /**
     * CheckHeartbeats constructor.
     *
     * @param HeartbeatRepositoryInterface $repository
     */
    public function __construct(HeartbeatRepositoryInterface $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     * @param Dispatcher $dispatcher
     */
    public function handle(Dispatcher $dispatcher)
    {
        $this->repository->chunk(10, function (Collection $heartbeats) use ($dispatcher) {
            $heartbeats->each(function ($heartbeat) use ($dispatcher) {
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

                    $dispatcher->dispatch(new HeartbeatMissed($heartbeat));
                }
            });
        });
    }
}
