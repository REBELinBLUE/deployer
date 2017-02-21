<?php

namespace REBELinBLUE\Deployer\Http\Requests;

/**
 * Request for validating servers.
 */
class StoreServerRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name'         => 'required|max:255',
            'user'         => 'required|max:255',
            'ip_address'   => 'required|host',
            'deploy_code'  => 'boolean',
            'port'         => 'required|integer|min:0|max:65535',
            'path'         => 'required',
            'add_commands' => 'boolean',
            'project_id'   => 'required|integer|exists:projects,id',
        ];

        if ($this->route('server')) {
            unset($rules['project_id']);
            unset($rules['add_commands']);
        }

        return $rules;
    }
}
