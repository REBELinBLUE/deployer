<?php

namespace REBELinBLUE\Deployer\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\User;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Project $project)
    {
        if ($user->hasPermissionTo('projects.*.view', $project->id)) {
            return true;
        }
    }

    public function deploy(User $user, Project $project)
    {
        return true;
    }
}
