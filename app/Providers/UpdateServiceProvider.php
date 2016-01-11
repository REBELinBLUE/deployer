<?php

namespace REBELinBLUE\Deployer\Providers;

use Httpful\Request;
use Illuminate\Support\ServiceProvider;

class UpdateServiceProvider extends ServiceProvider
{
    private $github_url = 'https://api.github.com/repos/REBELinBLUE/deployer/releases/latest';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Define a constant for the application version
        if (!defined('APP_VERSION')) {
            define('APP_VERSION', trim(file_get_contents(app_path('../VERSION'))));
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->getLatestRelease();
    }

    /**
     * Get's the latest release from github.
     *
     * @return void
     */
    public function getLatestRelease()
    {
        $response = Request::get($this->github_url)
                           ->addAcceptHeader('application/vnd.github.v3+json')
                           ->expectsJson()
                           ->send();

        //dd($response->body->tag_name);
    }
}
