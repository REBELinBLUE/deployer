<?php

namespace REBELinBLUE\Deployer\Http\Requests;

/**
 * Request for validating variables.
 */
class StoreVariableRequest extends Request
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
            'value'      => 'required',
            'project_id' => 'required|integer|exists:projects,id',
        ];
    }
}
