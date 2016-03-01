<?php

namespace REBELinBLUE\Deployer\Listeners\Events;

use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;
use REBELinBLUE\Deployer\Events\Event;
use REBELinBLUE\Deployer\Listeners\Events\Event as EventListener;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Event listener class to create JWT on login.
 */
class CreateJwt extends EventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login|JsonWebTokenExpired $event
     * @return void
     */
    public function handle(Event $event)
    {
        $tokenId    = base64_encode(str_random(32));
        $issuedAt   = Carbon::now()->timestamp;
        $notBefore  = $issuedAt;
        $expire     = $notBefore + 3 * 60 * 60; // Adding 3 hours

        $expire = $notBefore + 30;

        // Create the token
        $config = [
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'jti'  => $tokenId,          // JSON Token ID: an unique identifier for the token
            'iss'  => config('app.url'), // Issuer
            'nbf'  => $notBefore,        // Not before
            'exp'  => $expire,           // Expire
            'data' => [                  // Data related to the signed user
                'userId' => $event->user->id    // User ID from the users table
            ],
        ];

        Session::put('jwt', JWTAuth::fromUser($event->user, $config));
    }
}
