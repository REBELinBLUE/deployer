<?php

namespace REBELinBLUE\Deployer\Contracts\Github;

interface LatestReleaseInterface
{
    /**
     * @return false|string
     */
    public function latest();
}
