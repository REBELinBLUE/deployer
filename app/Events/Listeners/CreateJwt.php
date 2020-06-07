<?php

namespace REBELinBLUE\Deployer\Events\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Session\Store;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use Tymon\JWTAuth\JWTAuth;

/**
 * Event listener class to create JWT on login.
 */
class CreateJwt
{
    /**
     * @var JWTAuth
     */
    protected $auth;

    /**
     * @var Store
     */
    private $session;

    /**
     * Create a new middleware instance.
     *
     * @param JWTAuth                 $auth
     * @param Store                   $session
     * @param TokenGeneratorInterface $generator
     */
    public function __construct(JWTAuth $auth, Store $session)
    {
        $this->auth      = $auth;
        $this->session   = $session;
    }

    /**
     * Handle the event.
     *
     * @param Login $event
     */
    public function handle(Login $event): void
    {
        $this->session->put('jwt', $this->auth->fromUser($event->user));
    }
}
