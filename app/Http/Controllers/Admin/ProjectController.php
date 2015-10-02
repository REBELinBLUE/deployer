<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController as Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreProjectRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;

/**
 * The controller for managging projects.
 */
class ProjectController extends Controller
{
    /**
     * Class constructor.
     *
     * @param  ProjectRepositoryInterface $repository
     * @return void
     */
    public function __construct(ProjectRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Shows all projects.
     *
     * @param  TemplateRepositoryInterface $templateRepository
     * @param  GroupRepositoryInterface    $groupRepository
     * @return Response
     */
    public function index(
        TemplateRepositoryInterface $templateRepository,
        GroupRepositoryInterface $groupRepository
    ) {
        $projects = $this->repository->getAll();

        return view('admin.projects.listing', [
            'title'     => Lang::get('projects.manage'),
            'templates' => $templateRepository->getAll(),
            'groups'    => $groupRepository->getAll(),
            'projects'  => $projects->toJson(), // Because PresentableInterface toJson() is not working in the view
        ]);
    }

    /**
     * Store a newly created project in storage.
     *
     * @param  StoreProjectRequest $request
     * @return Response
     */
    public function store(StoreProjectRequest $request)
    {
        return $this->repository->create($request->only(
            'name',
            'repository',
            'branch',
            'group_id',
            'builds_to_keep',
            'url',
            'build_url',
            'template_id',
            'full_clone'
        ));
    }

    /**
     * Update the specified project in storage.
     *
     * @param  int                 $project_id
     * @param  StoreProjectRequest $request
     * @return Response
     */
    public function update($project_id, StoreProjectRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'repository',
            'branch',
            'group_id',
            'builds_to_keep',
            'url',
            'build_url',
            'full_clone'
        ), $project_id);
    }
}
