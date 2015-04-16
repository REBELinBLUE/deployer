<?php namespace App\Http\Controllers;

use Lang;
use Input;
use App\Command;
use App\Project;
use App\Group;
use App\Template;
use App\Deployment;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\DeploymentRepositoryInterface;
use App\Http\Requests\StoreProjectRequest;
use App\Commands\SetupProject;
use App\Commands\QueueDeployment;

/**
 * The controller for managing projects
 */
class ProjectController extends Controller
{
    /**
     * Shows all projects
     *
     * @param ProjectRepositoryInterface $projectRepository
     * @return Response
     */
    public function index(ProjectRepositoryInterface $projectRepository)
    {
        $projects = $projectRepository->getAll();

        foreach ($projects as $project) {
            $project->group_name = $project->group->name;
            $project->deploy     = Lang::get('app.never');

            if ($project->last_run) {
                $project->deploy = $project->last_run->format('jS F Y g:i:s A');
            }
        }

        return view('projects.listing', [
            'title'     => Lang::get('projects.manage'),
            'projects'  => $projects,
            'templates' => Template::all(),
            'groups'    => Group::all()
        ]);
    }

    /**
     * The details of an individual project
     *
     * @param Project $project
     * @param DeploymentRepositoryInterface $deploymentRepository
     * @return View
     */
    public function show(Project $project, DeploymentRepositoryInterface $deploymentRepository)
    {
        $commands = [
            Command::DO_CLONE    => null,
            Command::DO_INSTALL  => null,
            Command::DO_ACTIVATE => null,
            Command::DO_PURGE    => null
        ];

        $optional = [];

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

            if ($command->optional) {
                $optional[] = $command;
            }
        }

        // FIXME: Make project injected in the constructor so we don't have to keep passing it
        return view('projects.details', [
            'title'         => $project->name,
            'deployments'   => $deploymentRepository->getLatest($project),
            'today'         => $deploymentRepository->getTodayCount($project),
            'last_week'     => $deploymentRepository->getLastWeekCount($project),
            'project'       => $project,
            'servers'       => $project->servers,
            'notifications' => $project->notifications,
            'commands'      => $commands,
            'optional'      => $optional // FIXME: Is there a cleaner way to do this?
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
        $project = Project::create($request->only(
            'name',
            'repository',
            'branch',
            'group_id',
            'builds_to_keep',
            'url',
            'build_url'
        ));

        // FIXME: Should this be an event rather than a command?
        if ($request->has('template_id')) {
            $this->dispatch(new SetupProject(
                $project,
                Template::find($request->template_id)
            ));
        }

        $project->group_name = $project->group->name;
        $project->deploy     = Lang::get('app.never');

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

        $project->group_name = $project->group->name;
        $project->deploy     = Lang::get('app.never');

        if ($project->last_run) {
            $project->deploy = $project->last_run->format('jS F Y g:i:s A');
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
        $deployment->reason = Input::get('reason');

        $optional = [];

        if (Input::has('optional')) {
            $optional = Input::get('optional');
        }

        $this->dispatch(new QueueDeployment(
            $project,
            $deployment,
            $optional
        ));

        return redirect()->route('deployment', [
            'id' => $deployment->id
        ]);
    }
}
