<?php

namespace REBELinBLUE\Deployer\Http\Composers;

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
     * @param  \Illuminate\Contracts\View\View $view
     * @return void
     */
    public function compose(View $view)
    {
        $theme = config('deployer.theme');
        $user = Auth::user();

        if ($user) {
            if (!empty($user->skin)) {
                $theme = $user->skin;
            }
        }

        $view->with('theme', $theme);
    }
}
