<?php

namespace REBELinBLUE\Deployer\Github;

use Httpful\Request;
use Illuminate\Support\Facades\Cache;

/**
 * A class to get the latest release tag for Github.
 */
class LatestRelease
{
    const CACHE_TIME_IN_HOURS = 12;

    /**
     * @var string
     **/
    private $github_url = 'https://api.github.com/repos/REBELinBLUE/deployer/releases/latest';

    /**
     * Get the latest release from Github.
     *
     * @return string
     */
    public function latest()
    {
        $cache_for = self::CACHE_TIME_IN_HOURS * 60;

        $release = Cache::remember('latest_version', $cache_for, function () {

            $request = Request::get($this->github_url)
                              ->expectsJson()
                              ->withAccept('application/vnd.github.v3+json');

            if (config('deployer.github_oauth_token')) {
                $request->withAuthorization('token ' . config('deployer.github_oauth_token'));
            }

            $response = $request->send();

            // FIXME: handle errors, maybe throw an exception?
            return $response->body;
        });

        return $release->tag_name;
    }
}
