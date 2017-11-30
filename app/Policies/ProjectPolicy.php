<?php

namespace REBELinBLUE\Deployer\Policies;

use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Project $project)
    {
        return $project->can('view', $user);
    }

    public function deploy(User $user, Project $project)
    {
        return $project->can('deploy', $user);
    }

    public function update(User $user, Project $project)
    {
        return $project->can('update', $user);
    }

    public function manage(User $user, Project $project)
    {
        return $project->can('manage', $user);
    }
}
