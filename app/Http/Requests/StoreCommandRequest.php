<?php

namespace REBELinBLUE\Deployer\Http\Requests;

use REBELinBLUE\Deployer\Command;

/**
 * Request for validating commands.
 */
class StoreCommandRequest extends Request
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
            'user'        => 'max:255',
            'script'      => 'required',
            'optional'    => 'boolean',
            'default_on'  => 'boolean',
            'step'        => 'required|integer|min:' . Command::BEFORE_CLONE . '|max:' . Command::AFTER_PURGE,
            'target_type' => 'required|in:project,template',
            'target_id'   => 'required|integer|exists:' . $this->get('target_type') . 's,id',
        ];

        // On edit we don't require the step or the project_id
        if ($this->route('command')) {
            unset($rules['step']);
            unset($rules['target_id']);
            unset($rules['target_type']);
        }

        return $rules;
    }
}
