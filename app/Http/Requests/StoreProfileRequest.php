<?php

namespace REBELinBLUE\Deployer\Http\Requests;

/**
 * Validate the user name and password.
 */
class StoreProfileRequest extends Request
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
            'password' => 'required|confirmed|min:6|zxcvbn:3,name',
        ];

        if ($this->get('password') === '') {
            unset($rules['password']);
        }

        return $rules;
    }g
}
