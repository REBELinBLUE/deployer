<?php

namespace REBELinBLUE\Deployer\Http\Requests;

/**
 * Request for validating projects.
 */
class StoreProjectRequest extends Request
{
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
            'template_id'        => 'nullable|integer|exists:templates,id',
            'url'                => 'nullable|url',
            'build_url'          => 'nullable|url',
            'allow_other_branch' => 'boolean',
            'include_dev'        => 'boolean',
            'private_key'        => 'nullable|sshkey',
        ];
    }
}
