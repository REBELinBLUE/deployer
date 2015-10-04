<?php

namespace REBELinBLUE\Deployer\Presenters;

use Creativeorange\Gravatar\Facades\Gravatar;
use Robbo\Presenter\Presenter;

/**
 * The view presenter for a user class.
 */
class UserPresenter extends Presenter
{
    /**
     * Get the user avatar.
     *
     * @return string
     */
    public function presentAvatarUrl()
    {
        if ($this->object->avatar) {
            return url($this->object->avatar);
        }

        return Gravatar::get($this->object->email);
    }
}
