<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\User;
use PragmaRX\Google2FA\Vendor\Laravel\Facade as Google2FA;

/**
 * Authentication controller.
 * fixme: clean this up, not sure most of this is needed as the traits do the work!
 */
class AuthController extends Controller
{
    use AuthenticatesUsers, ThrottlesLogins;

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        if (Auth::guard($this->getGuard())->viaRemember()) {
            return redirect()->intended(route('dashboard'));
        }

        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $auth = Auth::guard($this->getGuard());

        $credentials = $this->getCredentials($request);

        if ($auth->validate($credentials)) {
            $auth->once($credentials);

            if (Auth::user()->hasTwoFactorAuthentication()) {
                Session::put('2fa_user_id', Auth::user()->id);

                $this->clearLoginAttempts($request);

                return redirect()->intended(route('two-factor'));
            }

            $auth->attempt($credentials, true);

            return $this->handleUserWasAuthenticated($request, true);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function getTwoFactorAuthentication()
    {
        return view('auth.twofactor');
    }

    public function postTwoFactorAuthentication(Request $request)
    {
        $user_id = Session::pull('2fa_user_id');

        if ($user_id) {
            $code = Input::get('2fa_code');

            $auth = Auth::guard($this->getGuard());
            $auth->loginUsingId($user_id);

            $valid = Google2FA::verifyKey(Auth::user()->google2fa_secret, $code);

            if ($valid) {
                return $this->handleUserWasAuthenticated($request, true);
            }

            $auth->logout();

            return redirect()->to('/')
                             ->withError('invalid token');
        }

        return redirect()->to('/')
                         ->withError('invalid token');
    }
}
