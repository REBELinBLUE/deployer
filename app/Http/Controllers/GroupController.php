<?php namespace App\Http\Controllers;

use Lang;
use App\Group;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupRequest;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(StoreGroupRequest $request)
    {
        $group = new Group;

        $group->name = $request->name;
        $group->save();

        $group->project_count = 0;

        return $group;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $group_id
     * @return Response
     */
    public function update($group_id, StoreGroupRequest $request)
    {
        $group = Group::findOrFail($group_id);

        $group->name = $request->name;
        $group->save();

        $group->project_count = count($group->projects);

        return $group;
    }
}
