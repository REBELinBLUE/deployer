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
        if (preg_match('/^(ssh|git|https?):\/\//', $value)) { // Plain old git repo
            return true;
        }

        if (preg_match('/^(.*)@(.*):(.*)\/(.*)\.git/', $value)) { // Gitlab
            return true;
        }

        /*
        TODO: improve these regexs, using the following stolen from PHPCI (sorry Dan!)
        'ssh': /git\@github\.com\:([a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-]+)\.git/,
        'git': /git\:\/\/github.com\/([a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-]+)\.git/,
        'http': /https\:\/\/github\.com\/([a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-]+)(\.git)?/

        'ssh': /git\@bitbucket\.org\:([a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-]+)\.git/,
        'http': /https\:\/\/[a-zA-Z0-9_\-]+\@bitbucket.org\/([a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-]+)\.git/,
        'anon': /https\:\/\/bitbucket.org\/([a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-]+)(\.git)?/
        */

        return false;
    }
}
