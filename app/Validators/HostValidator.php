<?php

namespace REBELinBLUE\Deployer\Validators;

/**
 * Class for validating server hostnames & IP addresses.
 */
class HostValidator implements ValidatorInterface
{
    /**
     * Validate that the host is either a hostname or IP valid.
     *
     * @param array $args
     *
     * @return bool
     */
    public function validate(...$args)
    {
        $value = $args[1];

        if (filter_var($value, FILTER_VALIDATE_IP)) {
            return true;
        }

        if (filter_var(gethostbyname($value), FILTER_VALIDATE_IP)) {
            return true;
        }

        return false;
    }
}
