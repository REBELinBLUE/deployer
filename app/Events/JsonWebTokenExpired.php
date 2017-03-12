<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\User;

/**
 * Event which is fired when the JSON web token expires.
 */
class JsonWebTokenExpired extends Login
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * JsonWebTokenExpired constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
