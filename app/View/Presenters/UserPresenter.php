<?php

namespace REBELinBLUE\Deployer\View\Presenters;

use Creativeorange\Gravatar\Gravatar;

/**
 * The view presenter for a user class.
 */
class UserPresenter extends Presenter
{
    private $gravatar;

    /**
     * UserPresenter constructor.
     *
     * @param mixed    $object
     * @param Gravatar $gravatar
     */
    public function __construct(Gravatar $gravatar)
    {
        $this->gravatar = $gravatar;
    }

    /**
     * Get the user avatar.
     *
     * @return string
     */
    public function presentAvatarUrl()
    {
        if ($this->getWrappedObject()->avatar) {
            return url($this->getWrappedObject()->avatar);
        }

        return $this->gravatar->get($this->getWrappedObject()->email);
    }
}
