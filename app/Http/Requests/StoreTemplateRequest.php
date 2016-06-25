<?php

namespace REBELinBLUE\Deployer\Http\Requests;

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
        return [
            'name' => 'required|max:255',
        ];
    }
}
