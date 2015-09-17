<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController as Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreTemplateRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;

/**
 * Controller for managing deployment template.
 */
class TemplateController extends Controller
{
    /**
     * Class constructor.
     *
     * @param  TemplateRepositoryInterface $repository
     * @return void
     */
    public function __construct(TemplateRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Shows all templates.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $templates = $this->repository->getAll();

        return view('admin.templates.listing', [
            'title'     => Lang::get('templates.manage'),
            'templates' => $templates->toJson(), // Because PresentableInterface toJson() is not working in the view
        ]);
    }

    /**
     * Show the template configuration.
     *
     * @param  int      $template_id
     * @return \Illuminate\View\View
     */
    public function show($template_id)
    {
        $template = $this->repository->getById($template_id);

        return view('admin.templates.details', [
            'breadcrumb' => [
                ['url' => url('admin/templates'), 'label' => Lang::get('templates.label')],
            ],
            'title'         => $template->name,
            'sharedFiles'   => $template->sharedFiles,
            'projectFiles'  => $template->projectFiles,
            'project'       => $template,
            'route'         => 'template.commands',
        ]);
    }

    /**
     * Store a newly created template in storage.
     *
     * @param  StoreTemplateRequest $request
     * @return Response
     */
    public function store(StoreTemplateRequest $request)
    {
        return $this->repository->create($request->only(
            'name'
        ));
    }

    /**
     * Update the specified template in storage.
     *
     * @param  int                  $template_id
     * @param  StoreTemplateRequest $request
     * @return Response
     */
    public function update($template_id, StoreTemplateRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name'
        ), $template_id);
    }
}
