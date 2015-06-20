<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Lang;

/**
 * User management controller.
 */
class UserController extends Controller
{
    /**
     * The group repository.
     *
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * Class constructor.
     *
     * @param  UserRepositoryInterface $userRepository
     * @return void
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
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
            'users' => $this->userRepository->getAll(),
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
        return $this->userRepository->create($request->only(
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
        return $this->userRepository->updateById($request->only(
            'name',
            'email',
            'password'
        ), $user_id);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int     $user_id
     * @return Response
     */
    public function destroy($user_id)
    {
        $this->userRepository->deleteById($user_id);

        return [
            'success' => true,
        ];
    }
}
