<?php

namespace REBELinBLUE\Deployer\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use REBELinBLUE\Deployer\Deployment;

/**
 * A class to handle caching the abort request.
 */
class AbortDeployment extends Job
{
    const CACHE_KEY_PREFIX = 'deployer:cancel-deploy:';

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * AbortDeployment constructor.
     *
     * @param Deployment $deployment
     */
    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
    }

    /**
     * Execute the job.
     *
     * @param CacheRepository $cache
     */
    public function handle(CacheRepository $cache)
    {
        $timestamp = Carbon::now()->getTimestamp();
        $key       = self::CACHE_KEY_PREFIX . $this->deployment->id;

        $cache->put($key, $timestamp, 3600);
    }
}
