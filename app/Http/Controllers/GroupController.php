<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;
use Input;
use Response;

use App\Group;

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
    public function store()
    {
        $rules = array(
            'name'     => 'required|unique:groups'
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        } else {
            $group = new Group;

            $group->name = Input::get('name');
            $group->save();

            return $group;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $group_id
     * @return Response
     */
    public function update($group_id)
    {
        $rules = array(
            'name' => 'required|max:255|unique:users,' . $group_id
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        } else {
            $group = Group::findOrFail($group_id);

            $group->name = Input::get('name');
            $group->save();

            return $group;
        }
    }
}
