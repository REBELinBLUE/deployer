<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Validation\Factory;

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
                if (preg_match('/^(git|https?):\/\//', $value)) { // Plain old git repo
                    return true;
                }

                if (preg_match('/^(.*)@(.*):(.*)\/(.*)\.git/', $value)) { // Gitlab
                    return true;
                }

                if (preg_match('/^[a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-\.]+$/', $value)) { // Github
                    return true;
                }

                if (preg_match('/^[a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-\.]+$/', $value)) { // Bitbucket
                    return true;
                }

                return false;
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
            'name'           => 'required|max:255',
            'repository'     => 'required|repository',
            'branch'         => 'required|max:255',
            'group_id'       => 'required|integer|exists:groups,id',
            'builds_to_keep' => 'required|integer|min:1|max:20',
            'template_id'    => 'integer|exists:projects,id,is_template,1',
            'url'            => 'url',
            'build_url'      => 'url',
        ];
    }
}
