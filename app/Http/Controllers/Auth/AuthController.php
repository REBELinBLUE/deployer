<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Vendor\Laravel\Facade as Google2FA;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\User;

/**
 * Authentication controller.
 * fixme: clean this up, not sure most of this is needed as the traits do the work!
 */
class AuthController extends Controller
{
    use AuthenticatesUsers, ThrottlesLogins;

    protected $redirectTo = '/';

    /**
     * Show the application login form.
     *
     * @return Response
     */
    public function getLogin()
    {
        if (Auth::guard($this->getGuard())->viaRemember()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postLogin(Request $request)
    {
        // $this->validate($request, [
        //     'email'    => 'required|email',
        //     'password' => 'required',
        // ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $auth = Auth::guard($this->getGuard());

        $credentials = $this->getCredentials($request);

        if ($auth->validate($credentials)) {
            $auth->once($credentials);

            if ($auth->user()->hasTwoFactorAuthentication()) {
                Session::put('2fa_user_id', $auth->user()->id);

                $this->clearLoginAttempts($request);

                return redirect()->route('auth.twofactor');
            }

            $auth->attempt($credentials, true);

            return $this->handleUserWasAuthenticated($request, true);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Shows the 2FA form.
     *
     * @return Response
     */
    public function getTwoFactorAuthentication()
    {
        return view('auth.twofactor');
    }

    /**
     * Validates the 2FA code.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postTwoFactorAuthentication(Request $request)
    {
        $user_id = Session::pull('2fa_user_id');

        if ($user_id) {
            $auth = Auth::guard($this->getGuard());

            $auth->loginUsingId($user_id);

            if (Google2FA::verifyKey($auth->user()->google2fa_secret, $request->get('2fa_code'))) {
                return $this->handleUserWasAuthenticated($request, true);
            }

            $auth->logout();

            return redirect()->route('login')
                             ->withError(Lang::get('auth.invalid_code'));
        }

        return redirect()->route('login')
                         ->withError(Lang::get('auth.invalid_code'));
    }
}
