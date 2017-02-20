<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
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
     * @var ProjectRepositoryInterface
     */
    private $repository;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * ClearOrphanMirrors constructor.
     *
     * @param ProjectRepositoryInterface $repository
     * @param Process                    $process
     * @param Filesystem                 $filesystem
     */
    public function __construct(ProjectRepositoryInterface $repository, Filesystem $filesystem)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $current_mirrors = new Collection();
        $this->repository->chunk(100, function (Collection $projects) use (&$current_mirrors) {
            $projects->transform(function (Project $item) {
                return $item->mirrorPath();
            });

            $current_mirrors = $current_mirrors->merge($projects);
        });

        $all_mirrors = new Collection($this->filesystem->glob(storage_path('app/mirrors') . '/*.git'));

        // Compare the 2 collections get a list of mirrors which are no longer in use
        $orphan_mirrors = $all_mirrors->diff($current_mirrors);

        $this->info('Found ' . $orphan_mirrors->count() . ' orphaned mirrors');

        // Now loop through the mirrors and delete them from storage
        $orphan_mirrors->each(function ($mirror_dir) {
            $name = $this->filesystem->basename($mirror_dir);

            if ($this->filesystem->deleteDirectory($mirror_dir)) {
                $this->info('Deleted ' . $name);
            } else {
                $this->error('Failed to delete ' . $name);
            }
        });
    }
}
