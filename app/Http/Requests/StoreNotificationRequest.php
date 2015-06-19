<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

/**
 * Request for validating notifications.
 * @fixme Check channel and webhook are valid
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
            'name'       => 'required|max:255',
            'channel'    => 'required|max:255',
            'webhook'    => 'required|url',
            'project_id' => 'required|integer|exists:projects,id',
        ];
    }
}
