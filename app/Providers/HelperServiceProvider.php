<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Service provider to load helpers.
 */
class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        /** @var \Illuminate\Filesystem\Filesystem $filesystem */
        $filesystem = app('files');
        foreach ($filesystem->glob(app_path('Helpers') . '/*Helper.php') as $filename) {
            $filesystem->requireOnce($filename);
        }
    }
}
