<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
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
     * @param  ViewFactory           $view
     * @param  Translator            $translator
     * @return \Illuminate\View\View
     */
    public function index(ViewFactory $view, Translator $translator)
    {
        return $view->make('admin.groups.listing', [
            'title'  => $translator->trans('groups.manage'),
            'groups' => $this->repository->getAll(),
        ]);
    }

    /**
     * Store a newly created group in storage.
     *
     * @param StoreGroupRequest $request
     * @param ResponseFactory   $response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreGroupRequest $request, ResponseFactory $response)
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
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($group_id, StoreGroupRequest $request)
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
    public function reorder(Request $request)
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
