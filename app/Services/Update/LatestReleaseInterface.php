<?php

namespace REBELinBLUE\Deployer\Services\Update;

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
