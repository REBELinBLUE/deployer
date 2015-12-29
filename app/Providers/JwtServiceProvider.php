<?php

namespace REBELinBLUE\Deployer\Providers;

use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * The service provider to generatr JWT.
 */
class JwtServiceProvider extends ServiceProvider
{
    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        // On login, generate a JWT and store in the session
        $events->listen('auth.login', function ($user, $remember) {
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

            Session::put('jwt', JWTAuth::fromUser($user, $config));
        });

        // On logout, removed the JWT from the session
        $events->listen('auth.logout', function ($user) {
            Session::forget('jwt');
        });
    }
}
