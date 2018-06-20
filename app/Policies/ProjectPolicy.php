<?php

namespace REBELinBLUE\Deployer\Policies;

use REBELinBLUE\Deployer\User;
use REBELinBLUE\Deployer\Project;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->is_admin === true) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the project.
     *
     * @param  \REBELinBLUE\Deployer\User  $user
     * @param  \REBELinBLUE\Deployer\Project  $project
     * @return mixed
     */
    public function view(User $user, Project $project)
    {
        return $project->users()->where('users.id', $user->id)->count() === 1;
    }

    /**
     * Determine whether the user can rollback the project.
     *
     * @param  \REBELinBLUE\Deployer\User  $user
     * @param  \REBELinBLUE\Deployer\Project  $project
     * @return mixed
     */
    public function rollback(User $user, Project $project)
    {
        return $project->users()->where('role', 'manager')->where('users.id', $user->id)->count() === 1;
    }

    /**
     * Determine whether the user can create projects.
     *
     * @param  \REBELinBLUE\Deployer\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the project.
     *
     * @param  \REBELinBLUE\Deployer\User  $user
     * @param  \REBELinBLUE\Deployer\Project  $project
     * @return mixed
     */
    public function update(User $user, Project $project)
    {
        return $project->users()->where('role', 'manager')->where('users.id', $user->id)->count() === 1;
    }

    /**
     * Determine whether the user can delete the project.
     *
     * @param  \REBELinBLUE\Deployer\User  $user
     * @param  \REBELinBLUE\Deployer\Project  $project
     * @return mixed
     */
    public function delete(User $user, Project $project)
    {
        //
    }
}
