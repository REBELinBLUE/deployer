<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;

/**
 * Checks for and cleans up orphaned avatar files.
 */
class ClearOrphanAvatars extends Command
{
    const KEEP_FILES_FOR_MINUTES = 15;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployer:purge-avatars';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purges out avatar images which are no longer in use by an account';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Connection
     */
    private $database;

    /**
     * ClearOldKeys constructor.
     *
     * @param Filesystem $filesystem
     * @param Connection $database
     */
    public function __construct(Filesystem $filesystem, Connection $database)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
        $this->database   = $database;
    }

    /**
     * Execute the console command.
     * Remove unused avatar files from disk.
     */
    public function handle()
    {
        // Build up a list of all avatar images
        $avatars = $this->filesystem->glob(public_path() . '/storage/*/*.*');

        // Remove the public_path() from the path so that they match values in the DB
        array_walk($avatars, function (&$avatar) {
            $avatar = str_replace(public_path(), '', $avatar);
        });

        $all_avatars = new Collection($avatars);

        // Get all avatars currently assigned
        $current_avatars = $this->database->table('users')->whereNotNull('avatar')->pluck('avatar');

        // Compare the 2 collections get a list of avatars which are no longer assigned
        $orphan_avatars = $all_avatars->diff($current_avatars);

        $this->info('Found ' . $orphan_avatars->count() . ' orphaned avatars');

        $minimum_age = Carbon::now()->subMinutes(self::KEEP_FILES_FOR_MINUTES)->timestamp;

        // Now loop through the avatars and delete them from storage
        $orphan_avatars->each(function ($avatar) use ($minimum_age) {
            $avatarPath = public_path() . $avatar;

            // Don't delete recently created files as they could be temp files from the uploader
            if ($this->filesystem->lastModified($avatarPath) >= $minimum_age) {
                $this->info('Skipping ' . $avatar);

                return;
            }

            if (!$this->filesystem->delete($avatarPath)) {
                $this->error('Failed to delete ' . $avatar);
            } else {
                $this->info('Deleted ' . $avatar);
            }
        });
    }
}
