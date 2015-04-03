<?php namespace App\Http\Controllers;

use Lang;
use Response;
use Carbon\Carbon;
use App\Project;
use App\Deployment;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Commands\QueueDeployment;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();

        foreach ($projects as $project) {
            $project->group_name = $project->group->name;
            $project->deploy = ($project->last_run ? $project->last_run->format('jS F Y g:i:s A') : 'Never');
        }

        return view('projects.listing', [
            'title'    => Lang::get('projects.managge'),
            'projects' => $projects
        ]);
    }

    /**
     * The details of an individual project
     *
     * @param int $project_id The ID of the project to display
     * @return \Illuminate\View\View
     */
    public function show($project_id)
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
            $when   = strtolower($steps[0]);

            if (!is_array($commands[$action])) {
                $commands[$action] = [];
            }

            if (!isset($commands[$action][$when])) {
                $commands[$action][$when] = [];
            }

            $commands[$action][$when][] = $command->name;
        }

        $deployments = Deployment::where('project_id', $project->id)
                                 ->take($project->builds_to_keep)
                                 ->orderBy('started_at', 'DESC')
                                 ->get();

        $now = Carbon::now();
        $lastWeek = Carbon::now()->subWeek();
        $yesterday = Carbon::now()->yesterday();

        $today = Deployment::where('project_id', $project->id)
                           ->where('started_at', '>=', $now->format('Y-m-d') . ' 00:00:00')
                           ->where('started_at', '<=', $now->format('Y-m-d') . ' 23:59:59')
                           ->count();

        $week = Deployment::where('project_id', $project->id)
                          ->where('started_at', '>=', $lastWeek->format('Y-m-d') . ' 00:00:00')
                          ->where('started_at', '<=', $yesterday->format('Y-m-d') . ' 23:59:59')
                          ->count();

        return view('projects.details', [
            'title'         => $project->name,
            'deployments'   => $deployments,
            'today'         => $today,
            'last_week'     => $week,
            'project'       => $project,
            'servers'       => $project->servers, // FIXME: Order by name
            'notifications' => $project->notifications, // FIXME: Order by name
            'commands'      => $commands
        ]);
    }

    public function store(StoreProjectRequest $request)
    {
        $project = new Project;
        $project->name           = $request->name;
        $project->repository     = $request->repository;
        $project->branch         = $request->branch;
        $project->group_id       = $request->group_id;
        $project->builds_to_keep = $request->builds_to_keep;
        $project->url            = $request->url;
        $project->build_url      = $request->build_url;

        $project->generateSSHKey();
        $project->generateHash();
        $project->save();

        $project->group_name     = $project->group->name;
        $project->deploy         = 'Never';

        return $project;
    }

    public function update($project_id, StoreProjectRequest $request)
    {
        $project = Project::findOrFail($project_id);
        $project->name           = $request->name;
        $project->repository     = $request->repository;
        $project->branch         = $request->branch;
        $project->group_id       = $request->group_id;
        $project->builds_to_keep = $request->builds_to_keep;
        $project->url            = $request->url;
        $project->build_url      = $request->build_url;

        $project->save();

        $project->group_name     = $project->group->name;
        $project->deploy         = ($project->last_run ? $project->last_run->format('jS F Y g:i:s A') : Lang::get('app.never'));

        return $project;
    }

    public function destroy($project_id)
    {
        $project = Project::findOrFail($project_id);
        $project->delete();

        return Response::json([
            'success' => true
        ], 200);
    }

    public function servers($project_id)
    {
        $project = Project::findOrFail($project_id);

        return $project->servers;
    }

    /**
     * FIXME: Don't allow this to run if there is already a pending deploy or no servers
     */
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
