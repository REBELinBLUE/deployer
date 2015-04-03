<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

use Input;

class StoreGroupRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|unique:groups'
        ];

        if (Input::get('group_id')) {
            $rules['name'] .= ',' . Input::get('group_id');
        }

        return $rules;
    }
}
