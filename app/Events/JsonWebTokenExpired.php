<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Auth\Events\Login as Event;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\User;

/**
 * Event which is fired when the JSON web token expires.
 */
class JsonWebTokenExpired extends Event
{
    use SerializesModels;

    public $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user  = $user;
    }
}
