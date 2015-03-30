<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('users.listing', [
            'title' => 'Manage users',
            'users' => User::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $user_id
     * @return Response
     */
    public function update($user_id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $user_id
     * @return Response
     */
    public function destroy($user_id)
    {
        //
    }
}
