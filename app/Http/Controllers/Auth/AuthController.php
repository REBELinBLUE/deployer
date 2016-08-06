<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Contracts\Google2FA as Google2FA;
use REBELinBLUE\Deployer\Http\Controllers\Controller;

/**
 * Authentication controller.
 */
class AuthController extends Controller
{
    use AuthenticatesUsers, ThrottlesLogins;

    /**
     * Where to redirect to once the login has been successful.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * @var Google2FA
     */
    private $google2fa;

    /**
     * AuthController constructor.
     *
     * @param Google2FA $google2fa
     */
    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa  = $google2fa;
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
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
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
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

            if ($auth->user()->has_two_factor_authentication) {
                Session::put('2fa_user_id', $auth->user()->id);
                Session::put('2fa_remember', $request->has('remember'));

                $this->clearLoginAttempts($request);

                return redirect()->route('auth.twofactor');
            }

            $auth->attempt($credentials, $request->has('remember'));

            return $this->handleUserWasAuthenticated($request, true);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Shows the 2FA form.
     *
     * @return \Illuminate\View\View
     */
    public function getTwoFactorAuthentication()
    {
        return view('auth.twofactor');
    }

    /**
     * Validates the 2FA code.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postTwoFactorAuthentication(Request $request)
    {
        $user_id  = Session::pull('2fa_user_id');
        $remember = Session::pull('2fa_login_remember');

        if ($user_id) {
            $auth = Auth::guard($this->getGuard());

            $auth->loginUsingId($user_id, $remember);

            if ($this->google2fa->verifyKey($auth->user()->google2fa_secret, $request->get('2fa_code'))) {
                return $this->handleUserWasAuthenticated($request, true);
            }

            $auth->logout();

            return redirect()->route('auth.login')
                             ->withError(Lang::get('auth.invalid_code'));
        }

        return redirect()->route('auth.login')
                         ->withError(Lang::get('auth.invalid_code'));
    }
}
