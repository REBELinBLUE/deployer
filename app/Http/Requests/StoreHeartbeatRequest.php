<?php

namespace REBELinBLUE\Deployer\Http\Requests;

use REBELinBLUE\Deployer\Http\Requests\Request;

/**
 * Request for validating heartbeats.
 */
class StoreHeartbeatRequest extends Request
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
            'interval'   => 'required|integer',
            'project_id' => 'required|integer|exists:projects,id',
        ];
    }
}
