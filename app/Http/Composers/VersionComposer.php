<?php

namespace REBELinBLUE\Deployer\Http\Composers;

use Illuminate\Contracts\View\View;

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
        $view->with('current_version', APP_VERSION);
    }
}
