<?php

namespace REBELinBLUE\Deployer\View\Composers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;

/**
 * View composer for the header bar.
 */
class ThemeComposer implements ViewComposerInterface
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
     * Generates the pending and deploying projects for the view.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $theme = config('deployer.theme');
        $user  = $this->auth->user();

        if ($user) {
            if (!empty($user->skin)) {
                $theme = $user->skin;
            }
        }

        $view->with('theme', $theme);
    }
}
