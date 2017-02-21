<?php

namespace REBELinBLUE\Deployer\Http\Requests;

/**
 * Request for validating groups.
 */
class StoreGroupRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|max:255|unique:groups,name',
        ];

        // On edit add the group ID to the rules
        if ($this->route('group')) {
            $rules['name'] .= ',' . $this->route('group');
        }

        return $rules;
    }
}
