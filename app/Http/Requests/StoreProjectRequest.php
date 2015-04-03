<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

use Auth;

class StoreProjectRequest extends Request
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
            'name'           => 'required',
            'repository'     => 'required',
            'branch'         => 'required',
            'group_id'       => 'required|integer|exists:groups,id',
            'builds_to_keep' => 'required|integer|min:1|max:20',
            'url'            => 'url',
            'build_url'      => 'url'
        ];
    }
}
