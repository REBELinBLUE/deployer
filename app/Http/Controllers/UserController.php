<?php namespace App\Http\Controllers;

use Response;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->created = $user->created_at->format('jS F Y g:i:s A');
        }

        return view('users.listing', [
            'title' => 'Manage users',
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(StoreUserRequest $request)
    {
        $user = new User;
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        $user->created = $user->created_at->format('jS F Y g:i:s A');

        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $user_id
     * @return Response
     */
    public function update($user_id, StoreUserRequest $request)
    {
        $user = User::findOrFail($user_id);

        $user->name  = $request->name;
        $user->email = $request->email;

        if (isset($request->password)) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        $user->created = $user->created_at->format('jS F Y g:i:s A');

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $user_id
     * @return Response
     */
    public function destroy($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->delete();

        return Response::json([
            'success'  => true,
            'redirect' => '/'
        ], 200);
    }
}
