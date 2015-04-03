<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Group;

use App\Http\Requests\StoreGroupRequest;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('groups.listing', [
            'title'  => 'Manage groups',
            'groups' => Group::all()
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

        return $group;
    }
}
