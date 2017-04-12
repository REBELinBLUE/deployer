<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreServerTemplateRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerTemplateRepositoryInterface;
use Illuminate\View\Factory as ViewFactory;

class ServerTemplateController extends Controller
{

    /**
     * ServerTemplateController constructor.
     *
     * @param ServerTemplateRepositoryInterface $repository
     */
    public function __construct(ServerTemplateRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param ViewFactory $view
     * @param Request     $request
     * @param Translator  $translator
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(ViewFactory $view, Request $request, Translator $translator)
    {
        return $view->make('admin.servers.listing', [
            'servers' => $this->repository->getAll(),
            'is_secure' => $request->isSecure(),
            'title' => $translator->trans('servers.manage')
        ]);
    }

    public function store(StoreServerTemplateRequest $request, ResponseFactory $response)
    {
        return $response->json($this->repository->create($request->only([
            'name',
            'ip_address',
            'port',
        ])));
    }

    public function update($server_template_id, StoreServerTemplateRequest $request) {
        return $this->repository->updateById($request->only([
            'name',
            'ip_address',
            'port',
        ]), $server_template_id);
    }
}
