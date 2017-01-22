<?php

namespace REBELinBLUE\Deployer\Validators;

/**
 * Class for validating git repository URLs.
 */
class RepositoryValidator
{
    /**
     * Validate that the repository URL looks valid.
     *
     * @param string $attribute
     * @param string $value
     * @param array  $parameters
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validate($attribute, $value, $parameters)
    {
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
