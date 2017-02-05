<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController as Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreProjectRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;

/**
 * The controller for managing projects.
 */
class ProjectController extends Controller
{
    /**
     * @var ViewFactory
     */
    private $view;

    /**
     * ProjectController constructor.
     *
     * @param ProjectRepositoryInterface $repository
     * @param ViewFactory                $view
     */
    public function __construct(ProjectRepositoryInterface $repository, ViewFactory $view)
    {
        $this->repository = $repository;
        $this->view       = $view;
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

        return $this->view->make('admin.projects.listing', [
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
