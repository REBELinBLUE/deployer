<?php

namespace REBELinBLUE\Deployer\View\Composers;

use Illuminate\Contracts\View\View;
use REBELinBLUE\Deployer\Services\Update\LatestReleaseInterface;

/**
 * View composer for the update bar.
 */
class VersionComposer implements ViewComposerInterface
{
    /**
     * @var LatestReleaseInterface
     */
    private $release;

    /**
     * VersionComposer constructor.
     *
     * @param LatestReleaseInterface $release
     */
    public function __construct(LatestReleaseInterface $release)
    {
        $this->release = $release;
    }

    /**
     * Determines if the update prompt should show.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $latest_tag = $this->release->latest();

        $current = APP_VERSION;
        $latest  = $latest_tag ?: APP_VERSION;

        $is_outdated = !($this->release->isUpToDate());

        $view->with('is_outdated', $is_outdated);
        $view->with('current_version', $current);
        $view->with('latest_version', $latest);
    }
}
