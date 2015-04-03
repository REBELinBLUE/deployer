<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class NotificationRequest extends Request
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'       => 'required',
            'channel'    => 'required',
            'webhook'    => 'required|url',
            'project_id' => 'required|integer|exists:projects,id'
        ];
    }
}
