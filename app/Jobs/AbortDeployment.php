<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Cache;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\Job;

/**
 * A class to handle caching the abort request.
 */
class AbortDeployment extends Job implements SelfHandling
{
    private $deployment;

    const CACHE_KEY_PREFIX = 'deployer:cancel-deploy:';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Cache::put(self::CACHE_KEY_PREFIX . $this->deployment->id, time(), 3600);
    }
}
