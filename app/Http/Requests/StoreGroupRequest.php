<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

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
        if ($this->get('id')) {
            $rules['name'] .= ',' . $this->get('id');
        }

        return $rules;
    }
}
