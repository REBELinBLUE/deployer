<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Auth;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Session\SessionManager; // FIXME: Shouldn't this be a contract?
use PragmaRX\Google2FA\Contracts\Google2FA;
use REBELinBLUE\Deployer\Http\Controllers\Controller;

/**
 * Controller for handling user login.
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
     * @var ViewFactory
     */
    private $view;

    /**
     * @var SessionManager
     */
    private $session;

    /**
     * @var Redirector
     */
    private $redirect;

    /**
     * AuthController constructor.
     *
     * @param Google2FA      $google2fa
     * @param ViewFactory    $view
     * @param SessionManager $session
     * @param Redirector     $redirect
     * @internal param Translator $translator
     */
    public function __construct(Google2FA $google2fa, ViewFactory $view, SessionManager $session, Redirector $redirect)
    {
        $this->google2fa  = $google2fa;
        $this->view       = $view;
        $this->session    = $session;
        $this->redirect   = $redirect;

        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        if ($this->guard()->viaRemember()) {
            return $this->redirect->route('dashboard');
        }

        return $this->view->make('auth.login');
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
                $this->session->put('2fa_user_id', $auth->user()->id);
                $this->session->put('2fa_remember', $request->has('remember'));

                $this->clearLoginAttempts($request);

                return $this->redirect->route('auth.twofactor');
            }

            $auth->attempt($credentials, $request->has('remember'));

            return $this->sendLoginResponse($request);
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
        return $this->view->make('auth.twofactor');
    }

    /**
     * Validates the 2FA code.
     *
     * @param Request $request
     *
     * @param  Translator                        $translator
     * @return \Illuminate\Http\RedirectResponse
     */
    public function twoFactorAuthenticate(Request $request, Translator $translator)
    {
        $user_id  = $this->session->pull('2fa_user_id');
        $remember = $this->session->pull('2fa_login_remember');

        if ($user_id) {
            $auth = $this->guard();

            $auth->loginUsingId($user_id, $remember);

            if ($this->google2fa->verifyKey($auth->user()->google2fa_secret, $request->get('2fa_code'))) {
                return $this->sendLoginResponse($request);
            }

            $auth->logout();

            return $this->redirect->route('auth.login')
                                  ->withError($translator->trans('auth.invalid_code'));
        }

        return $this->redirect->route('auth.login')
                              ->withError($translator->trans('auth.invalid_code'));
    }
}
