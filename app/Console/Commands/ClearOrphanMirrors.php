<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;

/**
 * Checks for and cleans up orphaned git mirrors.
 */
class ClearOrphanMirrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployer:purge-mirrors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purges git mirrors which are no longer in use by projects';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $current_mirrors = [];

        Project::where('is_template', false)->chunk(10, function ($projects) use (&$current_mirrors) {
            foreach ($projects as $project) {
                $current_mirrors[] = $project->mirrorPath();
            }
        });

        $current_mirrors = collect($current_mirrors);

        $all_mirrors = collect(glob(storage_path('app/mirrors/') . '*.git'));

        // Compare the 2 collections get a list of mirrors which are no longer in use
        $orphan_mirrors = $all_mirrors->diff($current_mirrors);

        $this->info('Found ' . $orphan_mirrors->count() . ' orphaned mirrors');

        // Now loop through the mirrors and delete them from storage
        foreach ($orphan_mirrors as $mirror_dir) {
            $process = new Process('tools.RemoveMirrorDirectory', [
                'mirror_path' => $mirror_dir,
            ]);
            $process->run();

            if ($process->isSuccessful()) {
                $this->info('Deleted ' . basename($mirror_dir));
            } else {
                $this->info('Failed to delete ' . basename($mirror_dir));
            }
        }
    }
}
