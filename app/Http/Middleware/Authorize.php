<?php

namespace REBELinBLUE\Deployer\Http\Middleware;

use Closure;

class Authorize
{
    private $resources = ['project', 'environment', 'command', 'variable', 'config_file', 'shared_file'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $ability = null)
    {
        $project = null;
        foreach ($this->resources as $key) {
            $module = $request->route($key);

            if ($module) {
                $project = $module;
                break;
            }
        }

//        dd($project);

//        if (!$project || !$project->can($ability, $request->user() ?: null)) {
//            return $this->unauthorized($request);
//        }

        return $next($request);
    }
}
