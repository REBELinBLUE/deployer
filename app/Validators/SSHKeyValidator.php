<?php

namespace REBELinBLUE\Deployer\Validators;

/**
 * Class for validating SSH private keys.
 */
class SSHKeyValidator implements ValidatorInterface
{
    /**
     * Validate that the SSH key looks valid.
     *
     * @param array $args
     *
     * @return bool
     */
    public function validate(...$args)
    {
        $value = trim($args[1]);

        // Check for start marker for SSH key
        if (!preg_match('/^-----BEGIN (.*) PRIVATE KEY-----/i', $value)) {
            return false;
        }

        // Check for end marker for SSH key
        if (!preg_match('/-----END (.*) PRIVATE KEY-----$/i', $value)) {
            return false;
        }

        // Make sure key does not have passphrase
        if (preg_match('/ENCRYPTED/i', $value)) {
            return false;
        }

        return true;
    }
}
