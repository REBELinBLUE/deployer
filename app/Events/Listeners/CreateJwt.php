<?php

namespace REBELinBLUE\Deployer\Events\Listeners;

use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Session\Store;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use Tymon\JWTAuth\JWTAuth;

/**
 * Event listener class to create JWT on login.
 */
class CreateJwt
{
    const LIFETIME_IN_HOURS = 3;

    /**
     * @var JWTAuth
     */
    protected $auth;

    /**
     * @var Store
     */
    private $session;

    /**
     * @var TokenGeneratorInterface
     */
    private $generator;

    /**
     * Create a new middleware instance.
     *
     * @param JWTAuth                 $auth
     * @param Store                   $session
     * @param TokenGeneratorInterface $generator
     */
    public function __construct(JWTAuth $auth, Store $session, TokenGeneratorInterface $generator)
    {
        $this->auth      = $auth;
        $this->session   = $session;
        $this->generator = $generator;
    }

    /**
     * Handle the event.
     *
     * @param Login $event
     */
    public function handle(Login $event)
    {
        $tokenId    = base64_encode($this->generator->generateRandom(32));
        $issuedAt   = Carbon::now()->timestamp;
        $notBefore  = $issuedAt;
        $expire     = $notBefore + self::LIFETIME_IN_HOURS * 60 * 60; // Adding 3 hours

        // Create the token
        $config = [
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'jti'  => $tokenId,          // JSON Token ID: an unique identifier for the token
            'iss'  => config('app.url'), // Issuer
            'nbf'  => $notBefore,        // Not before
            'exp'  => $expire,           // Expire
            'data' => [                  // Data related to the signed user
                'userId' => $event->user->id,    // User ID from the users table
            ],
        ];

        $this->session->put('jwt', $this->auth->fromUser($event->user, $config));
    }
}
