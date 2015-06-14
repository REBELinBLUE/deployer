<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserWasCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\StoreUserRequest;
use App\User;
use Lang;

/**
 * User management controller.
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
        return view('users.listing', [
            'title' => Lang::get('users.manage'),
            'users' => User::all()
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

        event(new UserWasCreated(
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
