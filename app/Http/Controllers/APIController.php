<?php


namespace REBELinBLUE\Deployer\Http\Controllers;

/**
 * The dashboard controller.
 */
class APIController extends Controller
{
    public function projects(ProjectRepositoryInterface $projectRepository)
    {
        return $projectRepository->getAll();
    }

    public function groups(GroupRepositoryInterface $groupRepository)
    {
        return $groupRepository->getAll();
    }
}
