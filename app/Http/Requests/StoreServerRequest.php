<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

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
            'name'       => 'required',
            'user'       => 'required',
            'ip_address' => 'required|ip',
            'path'       => 'required',
            'project_id' => 'required|integer|exists:projects,id'
        ];
    }
}
