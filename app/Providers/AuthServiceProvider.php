<?php

namespace REBELinBLUE\Deployer\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use REBELinBLUE\Deployer\Policies\ProjectPolicy;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\User;

/**
 * Add auth policy provider.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Project::class => ProjectPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param GateContract $gate
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        $gate->before(function (User $user, $ability) {

            // Allow the root user to have access to everything!
            if ($user->isSuperAdmin()) {
                return true;
            }

            // Define a generic "admin" ability which is true if any part of admin can be accessed
            if ($ability === 'admin') {
                return $user->can('admin.projects') ||
                       $user->can('admin.groups') ||
                       $user->can('admin.users') ||
                       $user->can('admin.templates');
            }
        });
    }
}
