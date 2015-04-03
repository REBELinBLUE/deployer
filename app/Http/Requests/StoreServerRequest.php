<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

use Auth;

class StoreServerRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

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
