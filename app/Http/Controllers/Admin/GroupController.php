<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController as Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreGroupRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;

/**
 * Group management controller.
 */
class GroupController extends Controller
{
    /**
     * Class constructor.
     *
     * @param  GroupRepositoryInterface $repository
     * @return void
     */
    public function __construct(GroupRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the groups.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.groups.listing', [
            'title'  => Lang::get('groups.manage'),
            'groups' => $this->repository->getAll(),
        ]);
    }

    /**
     * Store a newly created group in storage.
     *
     * @param  StoreGroupRequest $request
     * @return Response
     */
    public function store(StoreGroupRequest $request)
    {
        return $this->repository->create($request->only(
            'name'
        ));
    }

    /**
     * Update the specified group in storage.
     *
     * @param  int               $group_id
     * @param  StoreGroupRequest $request
     * @return Response
     */
    public function update($group_id, StoreGroupRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name'
        ), $group_id);
    }
}
