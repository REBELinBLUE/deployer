<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController;
use REBELinBLUE\Deployer\Http\Requests\StoreProjectRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * The controller for managing projects.
 */
class ProjectController extends Controller
{
    use ResourceController;

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
     * @param UserRepositoryInterface     $user
     * @param TemplateRepositoryInterface $templateRepository
     * @param GroupRepositoryInterface    $groupRepository
     * @param Request                     $request
     * @param ViewFactory                 $view
     * @param Translator                  $translator
     *
     * @return View
     */
    public function index(
        UserRepositoryInterface $user,
        TemplateRepositoryInterface $templateRepository,
        GroupRepositoryInterface $groupRepository,
        Request $request,
        ViewFactory $view,
        Translator $translator
    ): View {
        $projects = $this->repository->getAll(true);

        return $view->make('admin.projects.listing', [
            'is_secure' => $request->secure(),
            'title'     => $translator->get('projects.manage'),
            'templates' => $templateRepository->getAll(),
            'groups'    => $groupRepository->getAll(),
            'projects'  => $projects->toJson(),
            'users'     => $user->findNonAdminUsers()->toJson(),
        ]);
    }

    /**
     * Store a newly created project in storage.
     *
     * @param StoreProjectRequest $request
     * @param ResponseFactory     $response
     *
     * @return JsonResponse
     */
    public function store(StoreProjectRequest $request, ResponseFactory $response): JsonResponse
    {
        return $response->json($this->repository->create($request->only(
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
            'private_key',
            'managers',
            'users'
        )), Response::HTTP_CREATED);
    }

    /**
     * Update the specified project in storage.
     *
     * @param int                 $project_id
     * @param StoreProjectRequest $request
     *
     * @return Model
     */
    public function update(int $project_id, StoreProjectRequest $request): Model
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
            'private_key',
            'managers',
            'users'
        ), $project_id);
    }
}
