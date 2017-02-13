<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory as ViewFactory;
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
     * @var Dispatcher
     */
    private $dispatcher;
    /**
     * @var Translator
     */
    private $translator;

    /**
     * UserController constructor.
     *
     * @param UserRepositoryInterface $repository
     * @param ViewFactory             $view
     * @param Dispatcher              $dispatcher
     * @param Translator              $translator
     */
    public function __construct(
        UserRepositoryInterface $repository,
        ViewFactory $view,
        Dispatcher $dispatcher,
        Translator $translator
    ) {
        $this->repository = $repository;
        $this->view       = $view;
        $this->dispatcher = $dispatcher;
        $this->translator = $translator;
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->view->make('admin.users.listing', [
            'title' => $this->translator->trans('users.manage'),
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

        $this->dispatcher->dispatch(new UserWasCreated($user, $request->get('password')));

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
