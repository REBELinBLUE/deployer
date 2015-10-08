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
        $theme = env('APP_THEME', 'green');

        if (Auth::user()) {
            if (!empty(Auth::user()->skin)) {
                $theme = Auth::user()->skin;
            }
        }

        $view->with('theme', $theme);
    }
}
