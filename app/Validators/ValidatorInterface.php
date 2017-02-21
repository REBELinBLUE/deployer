<?php

namespace REBELinBLUE\Deployer\Validators;

interface ValidatorInterface
{
    /**
     * @param  array $args
     * @return bool
     */
    public function validate(...$args);
}
