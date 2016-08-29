<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ResetsPasswords;
use REBELinBLUE\Deployer\Http\Controllers\Controller;

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
}
