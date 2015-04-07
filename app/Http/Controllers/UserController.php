<?php namespace App\Http\Controllers;

use Lang;
use Response;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;

/**
 * User management controller
 */
class UserController extends Controller
{
    /**
     * Display a listing of the users.
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
            'title' => Lang::get('users.manage'),
            'users' => $users
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param StoreUserRequest $request
     * @return Response
     * @todo Use mass assignment if possible
     */
    public function store(StoreUserRequest $request)
    {
        $user = new User;
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        $user->created  = $user->created_at->format('jS F Y g:i:s A');

        return $user;
    }

    /**
     * Update the specified user in storage.
     *
     * @param User $user
     * @param StoreUserRequest $request
     * @return Response
     * @todo Use mass assignment if possible
     */
    public function update(User $user, StoreUserRequest $request)
    {
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
     * Remove the specified user from storage.
     *
     * @param User $user
     * @return Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return [
            'success' => true
        ];
    }
}
