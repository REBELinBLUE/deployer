<?php

namespace REBELinBLUE\Deployer\Composers;

use Illuminate\Contracts\View\View;
use REBELinBLUE\Deployer\Github\LatestRelease;
use Version\Version;

/**
 * View composer for the update bar.
 */
class VersionComposer
{
    /**
     * Determines if the update prompt should show.
     *
     * @param  \Illuminate\Contracts\View\View $view
     * @return void
     */
    public function compose(View $view)
    {
        $latest_tag = app(LatestRelease::class)->latest();

        $current = Version::parse(APP_VERSION);
        $latest  = Version::parse(APP_VERSION);

        if ($latest_tag) {
            $latest = Version::parse($latest_tag);
        }

        $is_outdated = ($latest->compare($current) === 1);

        $view->with('is_outdated', $is_outdated);
        $view->with('current_version', $current);
        $view->with('latest_version', $latest);
    }
}
