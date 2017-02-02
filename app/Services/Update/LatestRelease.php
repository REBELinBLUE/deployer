<?php

namespace REBELinBLUE\Deployer\Services\Update;

use GuzzleHttp\Client;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Version\Version;

/**
 * A class to get the latest release tag for Github.
 */
class LatestRelease implements LatestReleaseInterface
{
    const CACHE_TIME_IN_HOURS = 12;
    const CACHE_KEY           = 'latest_version';

    /**
     * @var string
     **/
    private $github_url = 'https://api.github.com/repos/REBELinBLUE/deployer/releases/latest';

    /**
     * @var CacheRepository
     */
    private $cache;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $token;

    /**
     * LatestRelease constructor.
     *
     * @param CacheRepository $cache
     * @param Client          $client
     * @param string          $token
     */
    public function __construct(CacheRepository $cache, Client $client, $token = null)
    {
        $this->cache  = $cache;
        $this->client = $client;
        $this->token  = $token;
    }

    /**
     * Get the latest release from Github.
     *
     * @return false|string
     */
    public function latest()
    {
        $cache_for = self::CACHE_TIME_IN_HOURS * 60;

        $release = $this->cache->remember(self::CACHE_KEY, $cache_for, function () {
            $headers = [
                'Accept' => 'application/vnd.github.v3+json',
            ];

            if (!is_null($this->token)) {
                $headers['Authorization'] = 'token ' . $this->token;
            }

            try {
                $response = $this->client->get($this->github_url, [
                    'headers' => $headers,
                ]);
            } catch (\Exception $exception) {
                return false;
            }

            return json_decode($response->getBody());
        });

        if (is_object($release) && property_exists($release, 'tag_name')) {
            return $release->tag_name;
        }

        return false;
    }

    /**
     * Returns whether or not the install is up to date.
     *
     * @return bool
     */
    public function isUpToDate()
    {
        $latest_release = $this->latest();

        $current = Version::parse(APP_VERSION);
        $latest  = Version::parse($latest_release ?: $current);

        return ($latest->compare($current) !== 1);
    }
}
