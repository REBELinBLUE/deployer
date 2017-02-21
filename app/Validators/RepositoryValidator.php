<?php

namespace REBELinBLUE\Deployer\Validators;

/**
 * Class for validating git repository URLs.
 */
class RepositoryValidator implements ValidatorInterface
{
    /**
     * Validate that the repository URL looks valid.
     *
     * @param array $args
     *
     * @return bool
     */
    public function validate(...$args)
    {
        $value = $args[1];

        // Plain old git repo
        if (preg_match('/^(ssh|git|https?):\/\//', $value)) {
            return true;
        }

        // Gitlab/Github
        if (preg_match('/^(.*)@(.*):(.*)\/(.*)\.git/', $value)) {
            return true;
        }

        return false;
    }
}
