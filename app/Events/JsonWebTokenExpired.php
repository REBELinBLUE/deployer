<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Queue\SerializesModels;

/**
 * Event which is fired when the JSON web token expires.
 */
class JsonWebTokenExpired extends Login
{
    use SerializesModels;

    /**
     * @var Authenticatable
     */
    public $user;

    /**
     * JsonWebTokenExpired constructor.
     *
     * @param string          $guard
     * @param Authenticatable $user
     * @param bool            $remember
     */
    public function __construct(string $guard, Authenticatable $user, bool $remember = false)
    {
        $this->user     = $user;
        $this->guard    = $guard;
        $this->remember = $remember;
    }
}
