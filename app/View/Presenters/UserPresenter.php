<?php

namespace REBELinBLUE\Deployer\View\Presenters;

use Creativeorange\Gravatar\Facades\Gravatar;
use Robbo\Presenter\Presenter;

/**
 * The view presenter for a user class.
 * @property string avatar_url
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
        if ($this->getObject()->avatar) {
            return url($this->getObject()->avatar);
        }

        return Gravatar::get($this->getObject()->email);
    }
}
