<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

/**
 * Request for validating servers
 */
class StoreServerRequest extends Request
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
            'user'       => 'required|max:255',
            'ip_address' => 'required|ip',
            'path'       => 'required',
            'project_id' => 'required|integer|exists:projects,id'
        ];
    }
}
