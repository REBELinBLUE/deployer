<?php

namespace REBELinBLUE\Deployer\Http\Requests;

/**
 * Request for validating servers.
 */
class StoreProjectFileRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => 'required|max:255',
            'path'        => 'required',
            'content'     => 'required',
            'target_type' => 'required|in:project,template',
            'target_id'   => 'required|integer|exists:' . $this->get('target_type') . 's,id'
        ];
    }
}
