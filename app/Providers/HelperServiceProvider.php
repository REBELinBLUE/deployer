<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Service provider to load helpers.
 */
class HelperServiceProvider extends ServiceProvider
{
    /**
     * Boot the application services.
     */
    public function boot()
    {
        /** @var \Illuminate\Filesystem\Filesystem $filesystem */
        $filesystem = $this->app->make('files');
        foreach ($filesystem->glob(app_path('Helpers') . '/*Helper.php') as $filename) {
            $filesystem->requireOnce($filename);
        }
    }
}
