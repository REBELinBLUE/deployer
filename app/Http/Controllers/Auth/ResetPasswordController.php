<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ResetsPasswords;
use REBELinBLUE\Deployer\Http\Controllers\Controller;

/**
 * Controller for handling password resets.
 */
class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect to once the password has been reset.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new password controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
<<<<<<< HEAD
     * {@inheritdoc}
=======
     * Get the password reset validation rules.
     *
     * @return array
>>>>>>> Add to requests
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6|zxcvbn:3,email',
        ];
    }
}
