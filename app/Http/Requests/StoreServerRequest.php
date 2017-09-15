<?php

namespace REBELinBLUE\Deployer\Http\Requests;

use Illuminate\Validation\Rule;
use REBELinBLUE\Deployer\Server;

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
            'name'               => 'required|max:255',
            'user'               => 'required|max:255',
            'ip_address'         => 'required|host',
            'deploy_code'        => 'boolean',
            'port'               => 'required|integer|min:0|max:65535',
            'path'               => 'required',
            'add_commands'       => 'boolean',
            'project_id'         => 'required|integer|exists:projects,id',
        ];

        if ($this->route('server')) {
            unset($rules['project_id'], $rules['add_commands']);
        }

        if (!empty($this->get('shared_server_id', null))) {
            unset($rules['name'], $rules['deploy_code'], $rules['port']);

            $rules['shared_server_id'] = [
                'integer',
                Rule::exists('servers')->where(function ($query) {
                    $query->where('id', $this->get('shared_server_id'))->where('type', Server::TYPE_SHARED);
                }),
            ];
        }

        return $rules;
    }
}
