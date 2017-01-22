<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Deployment;

/**
 * Deploy finished event.
 */
class DeploymentFinished
{
    use SerializesModels;

    /**
     * @var Deployment
     */
    public $deployment;

    /**
     * DeployFinished constructor.
     *
     * @param Deployment $deployment
     */
    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
    }
}
