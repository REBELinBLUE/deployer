<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController;
use REBELinBLUE\Deployer\Http\Requests\StoreUserRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * User management controller.
 */
class UserController extends Controller
{
    use ResourceController;

    /**
     * UserController constructor.
     *
     * @param UserRepositoryInterface $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the users.
     *
     * @param ViewFactory $view
     * @param Translator  $translator
     *
     * @return View
     */
    public function index(ViewFactory $view, Translator $translator): View
    {
        return $view->make('admin.users.listing', [
            'title' => $translator->get('users.manage'),
            'users' => $this->repository->getAll()->toJson(),
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param StoreUserRequest $request
     * @param Dispatcher       $dispatcher
     * @param ResponseFactory  $response
     *
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request, Dispatcher $dispatcher, ResponseFactory $response): JsonResponse
    {
        $user = $this->repository->create($request->only(
            'name',
            'email',
            'password',
            'is_admin'
        ));

        $dispatcher->dispatch(new UserWasCreated($user, $request->get('password')));

        return $response->json($user, Response::HTTP_CREATED);
    }

    /**
     * Update the specified user in storage.
     *
     * @param int              $user_id
     * @param StoreUserRequest $request
     *
     * @return Model
     */
    public function update(int $user_id, StoreUserRequest $request): Model
    {
        return $this->repository->updateById($request->only(
            'name',
            'email',
            'password',
            'is_admin'
        ), $user_id);
    }
}
