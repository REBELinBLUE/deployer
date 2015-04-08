<?php namespace App\Http\Controllers;

use Lang;
use Carbon\Carbon;
use App\Command;
use App\Project;
use App\Deployment;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Commands\QueueDeployment;

/**
 * The controller for managging projects
 */
class ProjectController extends Controller
{
    /**
     * Shows all projects
     *
     * @return Response
     */
    public function index()
    {
        $projects = Project::all();

        foreach ($projects as $project) {
            $project->group_name = $project->group->name;
            $project->deploy     = Lang::get('app.never');

            if ($project->last_run) {
                $project->deploy = $project->last_run->format('jS F Y g:i:s A');
            }
        }

        return view('projects.listing', [
            'title'    => Lang::get('projects.manage'),
            'projects' => $projects
        ]);
    }

    /**
     * The details of an individual project
     *
     * @param Project $project
     * @return View
     */
    public function show(Project $project)
    {
        $commands = [
            Command::DO_CLONE    => null,
            Command::DO_INSTALL  => null,
            Command::DO_ACTIVATE => null,
            Command::DO_PURGE    => null
        ];

        foreach ($project->commands as $command) {
            $action = $command->step - 1;
            $when = ($command->step % 3 === 0 ? 'after' : 'before');
            if ($when === 'before') {
                $action = $command->step + 1;
            }

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

        $now       = Carbon::now();
        $lastWeek  = Carbon::now()->subWeek();
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

    /**
     * Store a newly created project in storage.
     *
     * @param StoreProjectRequest $request
     * @return Response
     * @todo Use mass assignment if possible
     */
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
        $project->deploy         = Lang::get('app.never');

        return $project;
    }

    /**
     * Update the specified project in storage.
     *
     * @param Project $project
     * @param StoreProjectRequest $request
     * @return Response
     */
    public function update(Project $project, StoreProjectRequest $request)
    {
        $project->update($request->only(
            'name',
            'repository',
            'branch',
            'group_id',
            'builds_to_keep',
            'url',
            'build_url'
        ));

        $project->save();

        $project->group_name     = $project->group->name;
        $project->deploy         = Lang::get('app.never');

        if ($project->last_run) {
            $project->deploy     = $project->last_run->format('jS F Y g:i:s A');
        }

        return $project;
    }

    /**
     * Remove the specified project from storage.
     *
     * @param Project $project
     * @return Response
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return [
            'success' => true
        ];
    }

    /**
     * Adds a deployment for the specified project to the queue
     *
     * @param Project $project
     * @return Response
     * @todo Don't allow this to run if there is already a pending deploy or no servers
     */
    public function deploy(Project $project)
    {
        $deployment = new Deployment;

        $this->dispatch(new QueueDeployment($project, $deployment));

        return redirect()->route('deployment', [
            'id' => $deployment->id
        ]);
    }
}
