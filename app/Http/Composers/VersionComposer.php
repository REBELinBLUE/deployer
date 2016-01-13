<?php

namespace REBELinBLUE\Deployer\Http\Composers;

use Illuminate\Contracts\View\View;
use Version\Version;

/**
 * View composer for the footer bar.
 */
class VersionComposer
{
    /**
     * Generates the pending and deploying projects for the view.
     *
     * @param  \Illuminate\Contracts\View\View $view
     * @return void
     */
    public function compose(View $view)
    {
        $is_outdated = false;

        $current = Version::parse(APP_VERSION);
        $latest = Version::parse(APP_VERSION);

        $is_outdated = $latest->compare($current);



        $view->with('is_outdated', $is_outdated);
        $view->with('current_version', $current);
        $view->with('latest_version', $latest);
    }
}
