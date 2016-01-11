<?php

namespace REBELinBLUE\Deployer\Providers;

use Carbon\Carbon;
use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Auth\Events\Logout as LogoutEvent;
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
        // FIXME: Regenerate if it expires?
        $events->listen(LoginEvent::class, function ($user, $remember) {
            $tokenId    = base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
            $issuedAt   = Carbon::now()->timestamp;
            $notBefore  = $issuedAt;
            $expire     = $notBefore + 6 * 60 * 60; // Adding 6 hours

            // Create the token
            $config = [
                'iat'  => $issuedAt,         // Issued at: time when the token was generated
                'jti'  => $tokenId,          // JSON Token ID: an unique identifier for the token
                'iss'  => config('app.url'), // Issuer
                'nbf'  => $notBefore,        // Not before
                'exp'  => $expire,           // Expire
                'data' => [                  // Data related to the signed user
                    'userId' => $user->id    // User ID from the users table
                ],
            ];

            Session::put('jwt', JWTAuth::fromUser($user, $config));
        });

        // On logout, removed the JWT from the session
        $events->listen(LogoutEvent::class, function ($user) {
            Session::forget('jwt');
        });
    }
}
