<?php

namespace REBELinBLUE\Deployer\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * View composer for the active user.
 */
class ActiveUserComposer
{
    /**
     * Sets the logged in user into a view variable.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $user = Auth::user();
        $user->avatar_url = $user->getPresenter()->avatar_url;

        $view->with('logged_in_user', $user);
    }
}
