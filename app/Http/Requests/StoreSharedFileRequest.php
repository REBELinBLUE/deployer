<?php

namespace REBELinBLUE\Deployer\Http\Requests;

use REBELinBLUE\Deployer\Http\Requests\Request;

/**
 * Request for validating servers.
 */
class StoreSharedFileRequest extends Request
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
            'file'       => 'required',
            'project_id' => 'required|integer|exists:projects,id',
        ];
    }
}
