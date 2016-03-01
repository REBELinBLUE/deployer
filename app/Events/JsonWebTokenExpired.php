<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Events\Event;
use REBELinBLUE\Deployer\User;

class JsonWebTokenExpired extends Event
{
    use SerializesModels;

    public $user;
    public $token;

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
