<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTemplateRequest;
use App\Repositories\Contracts\TemplateRepositoryInterface;
use Lang;

/**
 * Controller for managing deployment template
 */
class TemplateController extends Controller
{
    /**
     * The template repository.
     *
     * @var TemplateRepositoryInterface
     */
    private $templateRepository;

    /**
     * Class constructor.
     *
     * @param TemplateRepositoryInterface $templateRepository
     * @return void
     */
    public function __construct(TemplateRepositoryInterface $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    /**
     * Shows all templates.
     *
     * @return Response
     */
    public function index()
    {
        $templates = $this->templateRepository->getAll();

        return view('templates.listing', [
            'title'     => Lang::get('templates.manage'),
            'templates' => $templates->toJson() // Because PresentableInterface toJson() is not working in the view
        ]);
    }

    /**
     * Show the template configuration
     *
     * @param int $template_id
     * @return Response
     */
    public function show($template_id)
    {
        $template = $this->templateRepository->getById($template_id);

        return view('templates.details', [
            'breadcrumb' => [
                ['url' => url('admin/templates'), 'label' => Lang::get('templates.label')],
            ],
            'title'         => $template->name,
            'sharedFiles'   => $template->shareFiles,
            'projectFiles'  => $template->projectFiles,
            'project'       => $template
        ]);
    }

    /**
     * Store a newly created template in storage.
     *
     * @param StoreTemplateRequest $request
     * @return Response
     */
    public function store(StoreTemplateRequest $request)
    {
        return $this->templateRepository->create($request->only(
            'name'
        ));
    }

    /**
     * Update the specified template in storage.
     *
     * @param int $template_id
     * @param StoreTemplateRequest $request
     * @return Response
     */
    public function update($template_id, StoreTemplateRequest $request)
    {
        return $this->templateRepository->updateById($request->only(
            'name'
        ), $template_id);
    }
}
