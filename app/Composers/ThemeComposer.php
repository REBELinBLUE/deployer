<?php

namespace REBELinBLUE\Deployer\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * View composer for body class
 */
class ThemeComposer
{
    /**
     * Generates the class for the skin
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $body = 'login-page';

        $theme = config('deployer.theme');
        $user = Auth::user();

        if ($user) {
            if (!empty($user->skin)) {
                $theme = $user->skin;
            }

            $body = 'skin-' . $theme;
        }

        $view->with('body', $body);
    }
}
