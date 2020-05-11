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
use REBELinBLUE\Deployer\Http\Requests\StoreGroupRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Group management controller.
 */
class GroupController extends Controller
{
    use ResourceController;

    /**
     * GroupController constructor.
     *
     * @param GroupRepositoryInterface $repository
     */
    public function __construct(GroupRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the groups.
     *
     * @param ViewFactory $view
     * @param Translator  $translator
     *
     * @return View
     */
    public function index(ViewFactory $view, Translator $translator): View
    {
        return $view->make('admin.groups.listing', [
            'title'  => $translator->get('groups.manage'),
            'groups' => $this->repository->getAll(),
        ]);
    }

    /**
     * Store a newly created group in storage.
     *
     * @param StoreGroupRequest $request
     * @param ResponseFactory   $response
     *
     * @return JsonResponse
     */
    public function store(StoreGroupRequest $request, ResponseFactory $response): JsonResponse
    {
        return $response->json($this->repository->create($request->only(
            'name'
        )), Response::HTTP_CREATED);
    }

    /**
     * Update the specified group in storage.
     *
     * @param int               $group_id
     * @param StoreGroupRequest $request
     *
     * @return Model
     */
    public function update(int $group_id, StoreGroupRequest $request): Model
    {
        return $this->repository->updateById($request->only(
            'name'
        ), $group_id);
    }

    /**
     * Re-generates the order for the supplied groups.
     *
     * @param Request $request
     *
     * @return array
     */
    public function reorder(Request $request): array
    {
        $order = 0;

        foreach ($request->get('groups') as $group_id) {
            $this->repository->updateById([
                'order' => $order,
            ], $group_id);

            $order++;
        }

        return [
            'success' => true,
        ];
    }
}
