<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Support\Facades\Cache;
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
     */
    public function handle()
    {
        Cache::put(self::CACHE_KEY_PREFIX . $this->deployment->id, time(), 3600);
    }
}
