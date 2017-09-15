<?php

namespace REBELinBLUE\Deployer\Http\Requests;

/**
 * Request for validating server templates.
 */
class StoreSharedServerRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'       => 'required|max:255',
            'user'       => 'max:255',
            'ip_address' => 'required|host',
            'port'       => 'required|integer|min:0|max:65535',
        ];
    }
}
