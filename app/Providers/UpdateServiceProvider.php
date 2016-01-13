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
        $request = Request::get($this->github_url)
                          ->expectsJson()
                          ->withAccept('application/vnd.github.v3+json');

        if (config('deployer.github_oauth_token')) {
            $request->withAuthorization('token ' . config('deployer.github_oauth_token'));
        }

        $response = $request->send();

        // FIXME: Obviously move this to a class, set it up to cache, handle errors etc
        define('LATEST_VERSION', $response->body->tag_name);
    }
}
