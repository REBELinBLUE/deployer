<?php

namespace App\Http\Composers;

use App\Group;
use App\Template;
use Illuminate\Contracts\View\View;

/**
 * View composer for the navigation bar.
 */
class NavigationComposer
{
    /**
     * Generates the group listing for the view.
     *
     * @param  \Illuminate\Contracts\View\View $view
     * @return void
     */
    public function compose(View $view)
    {
        $active_group   = null;
        $active_project = null;

        if (isset($view->project) && !$view->project->is_template) {
            $active_group   = $view->project->group_id;
            $active_project = $view->project->id;
        }

        $groups = Group::where('id', '<>', Template::GROUP_ID)
                       ->orderBy('name')
                       ->get();

        $view->with('active_group', $active_group);
        $view->with('active_project', $active_project);
        $view->with('groups', $groups);
    }
}
