<?php

namespace REBELinBLUE\Deployer\View\Presenters;

interface RuntimeInterface
{
    /**
     * Calculates the runtime for the task in seconds.
     *
     * @return int
     */
    public function runtime();
}
