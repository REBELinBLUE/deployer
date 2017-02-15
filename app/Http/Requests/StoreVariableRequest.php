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
        $rules = [
            'name'        => 'required|max:255',
            'value'       => 'required',
            'target_type' => 'required|in:project,template',
            'target_id'   => 'required|integer|exists:' . $this->get('target_type') . 's,id',
        ];

        if ($this->route('variable')) {
            unset($rules['target_type']);
            unset($rules['target_id']);
        }

        return $rules;
    }
}
