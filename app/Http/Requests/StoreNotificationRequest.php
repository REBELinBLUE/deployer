<?php

namespace REBELinBLUE\Deployer\Http\Requests;

use REBELinBLUE\Deployer\Http\Requests\Request;

/**
 * Request for validating notifications.
 */
class StoreNotificationRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'         => 'required|max:255',
            'channel'      => 'required|max:255|channel',
            'webhook'      => 'required|regex:/^https:\/\/hooks.slack.com' .
                              '\/services\/[a-z0-9]+\/[a-z0-9]+\/[a-z0-9]+$/i',
            'failure_only' => 'boolean',
            'project_id'   => 'required|integer|exists:projects,id',
        ];
    }
}
