<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Contracts\Google2FA as Google2FA;
use REBELinBLUE\Deployer\Http\Controllers\Controller;

/**
 * Controller for handling user login
 */
class LoginController extends Controller
{
    use AuthenticatesUsers;

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

        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        if ($this->guard()->viaRemember()) {
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
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $auth = $this->guard();

        $credentials = $this->credentials($request);

        if ($auth->validate($credentials)) {
            $auth->once($credentials);

            if ($auth->user()->has_two_factor_authentication) {
                Session::put('2fa_user_id', $auth->user()->id);
                Session::put('2fa_remember', $request->has('remember'));

                $this->clearLoginAttempts($request);

                return redirect()->route('auth.twofactor');
            }

            $auth->attempt($credentials, $request->has('remember'));

            return $this->sendLoginResponse($request, true);
        }

        if (!$lockedOut) {
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Shows the 2FA form.
     *
     * @return \Illuminate\View\View
     */
    public function showTwoFactorAuthenticationForm()
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
    public function twoFactorAuthenticate(Request $request)
    {
        $user_id  = Session::pull('2fa_user_id');
        $remember = Session::pull('2fa_login_remember');

        if ($user_id) {
            $auth = $this->guard();

            $auth->loginUsingId($user_id, $remember);

            if ($this->google2fa->verifyKey($auth->user()->google2fa_secret, $request->get('2fa_code'))) {
                return $this->sendLoginResponse($request, true);
            }

            $auth->logout();

            return redirect()->route('auth.login')
                             ->withError(Lang::get('auth.invalid_code'));
        }

        return redirect()->route('auth.login')
                         ->withError(Lang::get('auth.invalid_code'));
    }
}
