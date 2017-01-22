<?php

namespace REBELinBLUE\Deployer\View\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * View composer for the header bar.
 */
class ThemeComposer
{
    /**
     * Generates the pending and deploying projects for the view.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $theme = config('deployer.theme');
        $user  = Auth::user();

        if ($user) {
            if (!empty($user->skin)) {
                $theme = $user->skin;
            }
        }

        $view->with('theme', $theme);
    }
}
