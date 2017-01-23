<?php

namespace REBELinBLUE\Deployer\Services\Github;

interface LatestReleaseInterface
{
    /**
     * @return false|string
     */
    public function latest();

    /**
     * @return bool
     */
    public function isUpToDate();
}
