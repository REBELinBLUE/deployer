<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Contracts\Repositories\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\TemplateRepositoryInterface;
use REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController as Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreProjectRequest;

/**
 * The controller for managing projects.
 */
class ProjectController extends Controller
{
    /**
     * ProjectController constructor.
     *
     * @param ProjectRepositoryInterface $repository
     */
    public function __construct(ProjectRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Shows all projects.
     *
     * @param TemplateRepositoryInterface $templateRepository
     * @param GroupRepositoryInterface    $groupRepository
     * @param Request                     $request
     *
     * @return \Illuminate\View\View
     */
    public function index(
        TemplateRepositoryInterface $templateRepository,
        GroupRepositoryInterface $groupRepository,
        Request $request
    ) {
        $projects = $this->repository->getAll();

        return view('admin.projects.listing', [
            'is_secure' => $request->secure(),
            'title'     => Lang::get('projects.manage'),
            'templates' => $templateRepository->getAll(),
            'groups'    => $groupRepository->getAll(),
            'projects'  => $projects->toJson(), // Because PresentableInterface toJson() is not working in the view
        ]);
    }

    /**
     * Store a newly created project in storage.
     *
     * @param StoreProjectRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
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
            'allow_other_branch',
            'include_dev',
            'private_key'
        ));
    }

    /**
     * Update the specified project in storage.
     *
     * @param int                 $project_id
     * @param StoreProjectRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
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
            'allow_other_branch',
            'include_dev',
            'private_key'
        ), $project_id);
    }
}
