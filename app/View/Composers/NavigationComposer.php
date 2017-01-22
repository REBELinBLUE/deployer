<?php

namespace REBELinBLUE\Deployer\View\Composers;

use Illuminate\Contracts\View\View;
use REBELinBLUE\Deployer\Contracts\Repositories\GroupRepositoryInterface;

/**
 * View composer for the navigation bar.
 */
class NavigationComposer
{
    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * NavigationComposer constructor.
     *
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * Generates the group listing for the view.
     *
     * @param View $view
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
