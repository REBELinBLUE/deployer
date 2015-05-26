<?php namespace App\Http\Controllers\Admin;

use Lang;
use Event;
use App\User;
use App\Events\UserWasCreated;
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
     */
    public function store(StoreUserRequest $request)
    {
        $fields = $request->only(
            'name',
            'email'
        );

        $fields['password'] = bcrypt($request->password);

        $user = User::create($fields);

        Event::fire(new UserWasCreated(
            $user,
            $request->password
        ));

        $user->created = $user->created_at->format('jS F Y g:i:s A');

        return $user;
    }

    /**
     * Update the specified user in storage.
     *
     * @param User $user
     * @param StoreUserRequest $request
     * @return Response
     */
    public function update(User $user, StoreUserRequest $request)
    {
        $fields = $request->only(
            'name',
            'email'
        );

        if ($request->has('password')) {
            $fields['password'] = bcrypt($request->password);
        }

        $user->update($fields);

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
