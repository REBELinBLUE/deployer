<?php

namespace REBELinBLUE\Deployer\Http\Requests;

/**
 * Request for validating users.
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
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|confirmed|min:6|zxcvbn:3,name,email',
        ];

        // On edit change the password validator
        if ($this->route('user')) {
            $rules['email'] .= ',' . $this->route('user');

            if (!empty($this->get('password', null))) {
                $rules['password'] = 'min:6';
            } else {
                unset($rules['password']);
            }
        }

        return $rules;
    }
}
