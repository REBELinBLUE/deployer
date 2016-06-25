<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Http\Controllers\Controller;

/**
 * Password reset controller.
 * @property string subject
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
     */
    public function __construct()
    {
        $this->subject = Lang::get('emails.reset_subject'); // TODO: Is this right?

        $this->middleware('guest');
    }
}
