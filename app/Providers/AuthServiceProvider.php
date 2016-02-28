<?php

namespace REBELinBLUE\Deployer\Providers;

use REBELinBLUE\Deployer\User;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
        // 'REBELinBLUE\Deployer\Model' => 'REBELinBLUE\Deployer\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param GateContract $gate
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        // Allow the root user to have access to everything!
        $gate->before(function (User $user, $ability) {
            if ($user->hasRole('root')) {
                return true;
            }
        });
    }
}
