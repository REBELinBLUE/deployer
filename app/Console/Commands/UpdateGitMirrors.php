<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Jobs\UpdateGitMirror;
use REBELinBLUE\Deployer\Project;

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $last_mirrored_since = Carbon::now()->subMinutes(self::UPDATE_FREQUENCY_MINUTES);
        $todo                = self::UPDATES_TO_QUEUE;

        $projects = Project::where('last_mirrored', '<', $last_mirrored_since)->orWhereNull('last_mirrored');
        $projects->chunk($todo, function ($projects) {
            foreach ($projects as $project) {
                $this->dispatch(new UpdateGitMirror($project));
            }
        });
    }
}
