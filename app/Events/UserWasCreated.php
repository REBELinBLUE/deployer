<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Events\Event;
use REBELinBLUE\Deployer\User;

/**
 * Event which is fired when a user is created.
 */
class UserWasCreated extends Event
{
    use SerializesModels;

    /**
     * The user which was created.
     *
     * @var User
     */
    public $user;

    /**
     * The plain password, this is never stored on the model.
     *
     * @var string
     */
    public $password;

    /**
     * Create a new event instance.
     *
     * @param  User           $user
     * @param  string         $password
     * @return UserWasCreated
     */
    public function __construct(User $user, $password)
    {
        $this->user     = $user;
        $this->password = $password;
    }
}
