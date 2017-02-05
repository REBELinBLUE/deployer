<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController as Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreUserRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;

/**
 * User management controller.
 */
class UserController extends Controller
{
    /**
     * @var ViewFactory
     */
    private $view;

    /**
     * UserController constructor.
     *
     * @param UserRepositoryInterface $repository
     * @param ViewFactory             $view
     */
    public function __construct(UserRepositoryInterface $repository, ViewFactory $view)
    {
        $this->repository = $repository;
        $this->view       = $view;
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->view->make('admin.users.listing', [
            'title' => Lang::get('users.manage'),
            'users' => $this->repository->getAll()->toJson(), // PresentableInterface toJson() is not working in view
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param StoreUserRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->repository->create($request->only(
            'name',
            'email',
            'password'
        ));

        event(new UserWasCreated($user, $request->get('password')));

        return $user;
    }

    /**
     * Update the specified user in storage.
     *
     * @param int              $user_id
     * @param StoreUserRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
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
