<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Resources\ResourceController as Controller;
use App\Http\Requests\StoreUserRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Lang;

/**
 * User management controller.
 */
class UserController extends Controller
{
    /**
     * Class constructor.
     *
     * @param  UserRepositoryInterface $repository
     * @return void
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the users.
     *
     * @return Response
     */
    public function index()
    {
        return view('admin.users.listing', [
            'title' => Lang::get('users.manage'),
            'users' => $this->repository->getAll(),
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  StoreUserRequest $request
     * @return Response
     */
    public function store(StoreUserRequest $request)
    {
        return $this->repository->create($request->only(
            'name',
            'email',
            'password'
        ));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  int              $user_id
     * @param  StoreUserRequest $request
     * @return Response
     */
    public function update($user_id, StoreUserRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'email',
            'password'
        ), $user_id);
    }
}
