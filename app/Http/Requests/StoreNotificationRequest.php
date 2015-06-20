<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

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
        // FIXME: Check channel and webhook are valid
        return [
            'name'         => 'required|max:255',
            'channel'      => 'required|max:255',
            'webhook'      => 'required|url',
            'failure_only' => 'boolean',
            'project_id'   => 'required|integer|exists:projects,id',
        ];
    }
}
