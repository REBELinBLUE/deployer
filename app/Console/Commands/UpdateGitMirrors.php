<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Jobs\QueueUpdateGitMirror;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;

/**
 * Updates the mirrors for all git repositories.
 */
class UpdateGitMirrors extends Command
{
    use DispatchesJobs;

    const UPDATES_TO_QUEUE         = 3;
    const UPDATE_FREQUENCY_MINUTES = 5;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployer:update-mirrors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls in updates for git mirrors';

    /**
     * @var ProjectRepositoryInterface
     */
    private $repository;

    /**
     * UpdateGitMirrors constructor.
     * @param ProjectRepositoryInterface $repository
     */
    public function __construct(ProjectRepositoryInterface $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $last_since = Carbon::now()->subMinutes(self::UPDATE_FREQUENCY_MINUTES);

        $this->repository->getLastMirroredBefore($last_since, self::UPDATES_TO_QUEUE, function (Collection $projects) {
            $projects->each(function (Project $project) {
                $this->dispatch(new QueueUpdateGitMirror($project));
            });
        });
    }
}
