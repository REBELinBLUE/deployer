<?php

namespace REBELinBLUE\Deployer\Http\Requests;

/**
 * Request for validating check urls.
 */
class StoreCheckUrlRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name'       => 'required|max:255',
            'url'        => 'required|url',
            'period'     => 'required',
            'project_id' => 'required|integer|exists:projects,id',
        ];

        if ($this->route('check_url')) {
            unset($rules['project_id']);
        }

        return $rules;
    }
}
