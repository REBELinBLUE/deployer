<?php

namespace REBELinBLUE\Deployer\View\Presenters;

use Creativeorange\Gravatar\Gravatar;
use Robbo\Presenter\Presenter;

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
    public function __construct($object, Gravatar $gravatar)
    {
        parent::__construct($object);

        $this->gravatar = $gravatar;
    }

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

        return $this->gravatar->get($this->getObject()->email);
    }
}
