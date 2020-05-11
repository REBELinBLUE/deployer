<?php

namespace REBELinBLUE\Deployer\Http\Requests;

/**
 * Request for validating config files.
 */
class StoreConfigFileRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'name'        => 'required|max:255',
            'path'        => 'required',
            'content'     => 'required',
            'target_type' => 'required|in:project,template',
            'target_id'   => 'required|integer|exists:' . $this->get('target_type') . 's,id',
        ];

        if ($this->route('config_file')) {
            unset($rules['target_type']);
            unset($rules['target_id']);
        }

        return $rules;
    }
}
