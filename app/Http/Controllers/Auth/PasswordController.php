<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Auth;

use REBELinBLUE\Deployer\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Lang;

/**
 * Password reset controller.
 */
class PasswordController extends Controller
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
     *
     * @return void
     */
    public function __construct()
    {
        $this->subject = Lang::get('emails.reset_subject');

        $this->middleware('guest');
    }
}
