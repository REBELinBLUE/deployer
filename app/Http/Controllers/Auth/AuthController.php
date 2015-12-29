<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\User;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Authentication controller.
 */
class AuthController extends Controller
{
    use AuthenticatesUsers, ThrottlesLogins;

    /**
     * Where to redirect to once the user has been authenticated.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        if (Auth::viaRemember()) {
            redirect()->intended($this->redirectPath());
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

        $credentials = $this->getCredentials($request);
        if (Auth::attempt($credentials, true)) {
            $this->generateJWT($request);

            return redirect()->intended($this->redirectPath());
        }

        return redirect($this->loginPath())
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => $this->getFailedLoginMessage(),
            ]);
    }

    /**
     * Generates a JWT and stores it in the session
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     * @todo Move this, it should not really be here, maybe use an event, auth.login generate JWT and auth.logout clear it
     */
    private function generateJWT(Request $request)
    {
        $user       = Auth::user();

        $tokenId    = base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        $issuedAt   = Carbon::now()->timestamp;
        $notBefore  = $issuedAt;                 // Adding 10 seconds
        $expire     = $notBefore + 6 * 60 * 60;  // Adding 6 hours

        // Create the token
        $config = [
            'iat'  => $issuedAt,        // Issued at: time when the token was generated
            'jti'  => $tokenId,         // JSON Token Id: an unique identifier for the token
            'iss'  => env('APP_URL'),   // Issuer
            'nbf'  => $notBefore,       // Not before
            'exp'  => $expire,          // Expire
            'data' => [                 // Data related to the signed user
                'userId' => $user->id   // userid from the users table
            ],
        ];

        $request->session()->put('jwt', JWTAuth::fromUser($user, $config));
    }
}
