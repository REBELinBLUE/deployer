<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;

/**
 * Clears out any temp SSH keys and wrapper scripts which have been left on disk.
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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Clear out old SSH key files and archives
        $keys = glob(storage_path('app/') . '*ssh*');
        $archives = glob(storage_path('app/') . '*.tar.gz');

        $files   = array_merge($keys, $archives);
        $folders = glob(storage_path('app/') . '*clone*'); // cloned copies of code

        $this->info('Found ' . count($files) . ' files and ' . count($folders) . ' folders to purge');

        // Now loop through the temp files and delete them from storage
        foreach (array_merge($files, $folders) as $path) {
            $file = basename($path);

            // Don't delete recently created files as a precaution, 12 hours is more than enough
            if (filemtime($path) > strtotime('-12 hours')) {
                $this->info('Skipping ' . $file);
                continue;
            }

            $success = true;

            if (is_dir($path)) {
                if (!rmdir($path)) {
                    $this->error('Failed to delete folder ' . $file);
                    $success = false;
                }
            } elseif (!unlink($path)) {
                $this->error('Failed to delete file ' . $file);
                $success = false;
            }

            if ($success) {
                $this->info('Deleted ' . $file);
            }
        }
    }
}
