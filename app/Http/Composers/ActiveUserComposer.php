<?php

namespace REBELinBLUE\Deployer\Http\Composers;

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
     * @param  \Illuminate\Contracts\View\View $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('logged_in_user', Auth::user());
    }
}
