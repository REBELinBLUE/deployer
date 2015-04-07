<?php namespace App\Http\Requests;

use Input;
use App\Http\Requests\Request;

/**
 * Request for validating users
 */
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
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6'
        ];

        // On edit change the password validator
        if ($this->get('id')) {
            $rules['email'] .= ',' . $this->get('id');
            
            if (Input::get('password') !== '') {
                $rules['password'] = 'min:6';
            }
        }

        return $rules;
    }
}
