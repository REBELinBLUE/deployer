<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

use Input;

class StoreUserRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name'     => 'required',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6'
        ];

        // Editing users
        if (Input::get('user_id')) {
            $rules['email'] .= ',' . Input::get('user_id');
            
            if (Input::get('password') === '') {
                $rules['password'] = 'min:6';
            }
        }

        return $rules;
    }
}
