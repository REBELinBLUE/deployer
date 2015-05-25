<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

/**
 * Request for validating notify_email
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
            'project_id' => 'required|integer|exists:projects,id'
        ];
    }
}
