<?php

namespace REBELinBLUE\Deployer\Http\Requests;

/**
 * Request for validating notify_email.
 */
class StoreNotifyEmailRequest extends Request
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
            'email'      => 'required|email',
            'project_id' => 'required|integer|exists:projects,id',
        ];
    }
}
