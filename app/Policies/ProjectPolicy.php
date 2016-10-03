<?php

namespace REBELinBLUE\Deployer\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\User;

/**
 * Policy for projects
 */
class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * View permission.
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function view(User $user, Project $project)
    {
        if ($user->hasPermissionTo('projects.*.view', $project->id)) {
            return true;
        }
    }

    /**
     * Deploy permission.
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function deploy(User $user, Project $project)
    {
        return true;
    }

    /**
     * Manage permission.
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function manage(User $user, Project $project)
    {
        return true;
    }
}
