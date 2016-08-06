<?php

namespace REBELinBLUE\Deployer\Contracts;

interface RuntimeInterface
{
    /**
     * Calculates the runtime for the task in seconds.
     *
     * @return int
     */
    public function runtime();
}
