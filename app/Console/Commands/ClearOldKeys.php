<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;

/**
 * Clears out any temp SSH keys and wrapper scripts which have been left on disk
 */
class ClearOldKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployer:purge-temp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears out any temp SSH key files and wrapper scripts.';

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
        // Clear out old SSH key files
        $files = glob(storage_path() . '/app/*ssh*'); // sshkey and gitssh

        $this->info('Found ' . count($files) . ' files to purge');

        // Now loop through the temp files and delete them from storage
        foreach ($files as $filePath) {
            $file = basename($filePath);

            // Don't delete recently created files as a precaution, 12 hours is more than enough
            if (filemtime($filePath) > strtotime('-12 hours')) {
                $this->info('Skipping ' . $file);
                continue;
            }

            if (!unlink($filePath)) {
                $this->error('Failed to delete ' . $file);
            } else {
                $this->info('Deleted ' . $file);
            }
        }
    }
}
