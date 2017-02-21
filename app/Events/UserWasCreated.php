<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\User;

/**
 * Event which is fired when a user is created.
 */
class UserWasCreated
{
    use SerializesModels;

    /**
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
     * UserWasCreated constructor.
     *
     * @param User   $user
     * @param string $password
     */
    public function __construct(User $user, $password)
    {
        $this->user     = $user;
        $this->password = $password;
    }
}
