<?php namespace App\Http\Controllers;

use Lang;
use App\Group;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\GroupRepository;
use App\Http\Requests\StoreGroupRequest;
use Illuminate\Http\Request;

/**
 * Group management controller
 */
class GroupController extends Controller
{
    /**
     * Display a listing of the groups.
     *
     * @return Response
     */
    public function index(GroupRepository $groupRepository)
    {
        return view('groups.listing', [
            'title'  => Lang::get('groups.manage'),
            'groups' => $groupRepository->getAll()
        ]);
    }

    /**
     * Store a newly created group in storage.
     *
     * @param StoreGroupRequest $request
     * @return Response
     */
    public function store(StoreGroupRequest $request)
    {
        return Group::create($request->only(
            'name'
        ));
    }

    /**
     * Update the specified group in storage.
     *
     * @param Group $group
     * @param StoreGroupRequest $request
     * @return Response
     */
    public function update(Group $group, StoreGroupRequest $request)
    {
        $group->update($request->only(
            'name'
        ));

        return $group;
    }
}
