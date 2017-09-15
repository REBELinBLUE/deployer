<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\View\Factory as ViewFactory;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreSharedServerRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\SharedServerRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * The controller for managing servers.
 */
class SharedServerController extends Controller
{
    /**
     * SharedServerController constructor.
     *
     * @param SharedServerRepositoryInterface $repository
     */
    public function __construct(SharedServerRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param ViewFactory $view
     * @param Translator  $translator
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(ViewFactory $view, Translator $translator)
    {
        return $view->make('admin.servers.listing', [
            'servers' => $this->repository->getAll(),
            'title'   => $translator->trans('servers.manage'),
        ]);
    }

    /**
     * @param StoreSharedServerRequest $request
     * @param ResponseFactory          $response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSharedServerRequest $request, ResponseFactory $response)
    {
        return $response->json($this->repository->create($request->only(
            'name',
            'user',
            'ip_address',
            'port',
            'path'
        )), Response::HTTP_CREATED);
    }

    /**
     * @param int                      $server_id
     * @param StoreSharedServerRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($server_id, StoreSharedServerRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'user',
            'ip_address',
            'port',
            'path'
        ), $server_id);
    }
}
