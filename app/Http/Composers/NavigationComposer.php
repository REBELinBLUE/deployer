<?php namespace App\Http\Composers;

use App\Group;
use Illuminate\Contracts\View\View;

/**
 * View composer for the navigation bar
 */
class NavigationComposer
{
    /**
     * Generates the group listing for the view
     *
     * @param \Illuminate\Contracts\View\View $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('groups', Group::orderBy('name')->get());
    }
}
