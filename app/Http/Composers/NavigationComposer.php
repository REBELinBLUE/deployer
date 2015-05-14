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
        $active_group = null;
        $active_project = null;
        if (isset($view->project)) {
            $active_group = $view->project->group_id;
            $active_project = $view->project->id;
        }

        $view->with('active_group', $active_group);
        $view->with('active_project', $active_project);
        $view->with('groups', Group::orderBy('name')->get());
    }
}
