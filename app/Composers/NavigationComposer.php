<?php

namespace REBELinBLUE\Deployer\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;

/**
 * View composer for the navigation bar.
 */
class NavigationComposer
{
    private $groupRepository;

    /**
     * Class constructor.
     *
     * @param DeploymentRepositoryInterface $user
     */
    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

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

        $view->with('active_group', $active_group);
        $view->with('active_project', $active_project);
        $view->with('groups', $this->groupRepository->getAll());
    }
}
