<?php

namespace REBELinBLUE\Deployer\Http\Requests;

use Illuminate\Validation\Factory;
use REBELinBLUE\Deployer\Http\Requests\Request;

/**
 * Request for validating projects.
 */
class StoreProjectRequest extends Request
{
    /**
     * Overwrite the parent constructor to define a new validator.
     *
     * @param  Factory $factory
     * @return void
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function __construct(Factory $factory)
    {
        $factory->extend(
            'repository',
            function ($attribute, $value, $parameters) {
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
                */
                if (preg_match('/^[a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-\.]+$/', $value)) { // Github
                    return true;
                }

                /*
                'ssh': /git\@bitbucket\.org\:([a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-]+)\.git/,
                'http': /https\:\/\/[a-zA-Z0-9_\-]+\@bitbucket.org\/([a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-]+)\.git/,
                'anon': /https\:\/\/bitbucket.org\/([a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-]+)(\.git)?/
                */
                if (preg_match('/^[a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-\.]+$/', $value)) { // Bitbucket
                    return true;
                }

                return false;
            }
        );

        $factory->extend(
            'sshkey',
            function ($attribute, $value, $parameters) {
                $value = trim($value);

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
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'               => 'required|max:255',
            'repository'         => 'required|repository',
            'branch'             => 'required|max:255',
            'group_id'           => 'required|integer|exists:groups,id',
            'builds_to_keep'     => 'required|integer|min:1|max:20',
            'template_id'        => 'integer|exists:projects,id,is_template,1',
            'url'                => 'url',
            'build_url'          => 'url',
            'allow_other_branch' => 'boolean',
            'include_dev'        => 'boolean',
            'private_key'        => 'sshkey',
        ];
    }
}
