<?php

namespace REBELinBLUE\Deployer\Http\Validators;

/**
 * Class for validating server hostnames & IP addresses.
 */
class HostValidator
{
    /**
     * Validate that the host is either a hostname or IP valid.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     * @return bool
     */
    public function validate($attribute, $value, $parameters)
    {
        if (filter_var($value, FILTER_VALIDATE_IP)) {
            return true;
        }

        if (filter_var(gethostbyname($value), FILTER_VALIDATE_IP)) {
            return true;
        }

        return false;
    }
}
