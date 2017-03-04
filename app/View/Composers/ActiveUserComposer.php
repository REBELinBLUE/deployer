<?php

namespace REBELinBLUE\Deployer\View\Composers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;

/**
 * View composer for the active user.
 */
class ActiveUserComposer implements ViewComposerInterface
{
    /**
     * @var Guard
     */
    private $auth;

    /**
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Sets the logged in user into a view variable.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $view->with('logged_in_user', $this->auth->user());
    }
}
