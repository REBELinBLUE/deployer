<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Project;
use App\Deployment;

use App\Commands\QueueDeployment;

use Carbon\Carbon;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * The details of an individual project
     *
     * @param int $project_id The ID of the project to display
     * @return \Illuminate\View\View
     */
    public function details($project_id)
    {
        $project = Project::findOrFail($project_id);

        $commands = [
            'clone'     => null,
            'install'   => null,
            'activate'  => null,
            'purge'     => null
        ];

        foreach ($project->commands as $command) {
            // FIXME: There has to be a cleaner way to do this surely? Maybe on the model
            $steps  = explode(' ', $command->step);
            $action = strtolower($steps[1]);
            $when   =  strtolower($steps[0]);

            if (!is_array($commands[$action])) {
                $commands[$action] = [];
            }

            if (!isset($commands[$action][$when])) {
                $commands[$action][$when] = [];
            }

            $commands[$action][$when][] = $command->name;
        }

        $deployments = Deployment::where('project_id', '=', $project->id)
                                 ->take($project->builds_to_keep)
                                 ->orderBy('started_at', 'DESC')
                                 ->get();

        $now = Carbon::now();
        $lastWeek = Carbon::now()->subWeek();
        $yesterday = Carbon::now()->yesterday();

        $today = Deployment::where('project_id', '=', $project->id)
                           ->where('started_at', '>=', $now->format('Y-m-d') . ' 00:00:00')
                           ->where('started_at', '<=', $now->format('Y-m-d') . ' 23:59:59')
                           ->count();

        $week = Deployment::where('project_id', '=', $project->id)
                          ->where('started_at', '>=', $lastWeek->format('Y-m-d') . ' 00:00:00')
                          ->where('started_at', '<=', $yesterday->format('Y-m-d') . ' 23:59:59')
                          ->count();

        return view('project.details', [
            'title'              => $project->name,
            'deployments'        => $deployments,
            'today'              => $today,
            'last_week'          => $week,
            'project'            => $project,
            'servers'            => $project->servers, // Order by name
            'commands'           => $commands,
            'is_project_details' => true
        ]);
    }

    public function deploy($project_id)
    {
        $project = Project::findOrFail($project_id);
        $deployment = new Deployment;

        $this->dispatch(new QueueDeployment($project, $deployment));

        return redirect()->route('deployment', [
            'id' => $deployment->id
        ]);
    }
}
