<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\User;

/**
 * Event for user request to change the login email.
 */
class EmailChangeRequested
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * EmailChangeRequested constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
