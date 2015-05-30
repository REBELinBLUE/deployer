<?php namespace App\Http\Controllers\Admin;

use Lang;
use App\Project;
use App\Template;
use App\Group;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Http\Requests\StoreProjectRequest;
use App\Commands\SetupProject;

/**
 * The controller for managging projects
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

        return view('projects.listing', [
            'title'     => Lang::get('projects.manage'),
            'templates' => Template::all(),
            'groups'    => Group::all(),
            'projects'  => $projects->toJson() // Because PresentableInterface toJson() is not working in the view
        ]);
    }

    /**
     * Store a newly created project in storage.
     *
     * @param StoreProjectRequest $request
     * @return Response
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
}
