<?php namespace App\Http\Controllers;

use Lang;
use App\Group;
use App\Http\Requests;
use App\Http\Controllers\Controller;
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
    public function index()
    {
        $groups = Group::all();

        foreach ($groups as $group) {
            $group->project_count = count($group->projects);
        }

        return view('groups.listing', [
            'title'  => Lang::get('groups.manage'),
            'groups' => $groups
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
        $group = Group::create($request->only('name'));

        $group->project_count = 0;

        return $group;
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
        $group->update($request->only('name'));

        $group->project_count = count($group->projects);

        return $group;
    }
}
