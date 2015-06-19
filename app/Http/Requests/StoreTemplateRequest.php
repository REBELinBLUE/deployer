<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

/**
 * Request for validating groups.
 */
class StoreTemplateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|max:255',
        ];

        return $rules;
    }
}
