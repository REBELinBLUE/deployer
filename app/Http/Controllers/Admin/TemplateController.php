<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Contracts\Repositories\CommandRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\TemplateRepositoryInterface;
use REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController as Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreTemplateRequest;

/**
 * Controller for managing deployment template.
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
     * The command repository.
     *
     * @var CommandRepositoryInterface
     */
    protected $repository;

    /**
     * TemplateController constructor.
     *
     * @param CommandRepositoryInterface $commandRepository
     * @param TemplateRepositoryInterface $templateRepository
     */
    public function __construct(
        CommandRepositoryInterface $commandRepository,
        TemplateRepositoryInterface $templateRepository
    ) {
        $this->repository         = $commandRepository;
        $this->templateRepository = $templateRepository;
    }


    /**
     * Display a listing of before/after commands for the supplied stage.
     *
     * @param int $target_id
     * @param int $action
     *
     * @return \Illuminate\View\View
     */
    public function listing($target_id, $action)
    {
        $types = [
            'clone'    => Command::DO_CLONE,
            'install'  => Command::DO_INSTALL,
            'activate' => Command::DO_ACTIVATE,
            'purge'    => Command::DO_PURGE,
        ];

        $template = $this->templateRepository->getById($target_id);
        $target = 'template';

        $breadcrumb = [
            ['url' => route('admin.templates.index'), 'label' => Lang::get('templates.label')],
            ['url' => route('admin.templates.show', ['templates' => $template->id]), 'label' => $template->name],
        ];

        return view('commands.listing', [
            'breadcrumb'  => $breadcrumb,
            'title'       => Lang::get('commands.' . strtolower($action)),
            'subtitle'    => $template->name,
            'project'     => $template,
            'target_type' => $target,
            'target_id'   => $template->id,
            'action'      => $types[$action],
            'commands'    => $this->repository->getForDeployStep($template->id, $target, $types[$action]),
        ]);
    }

    /**
     * Shows all templates.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $templates = $this->templateRepository->getAll();

        return view('admin.templates.listing', [
            'title'     => Lang::get('templates.manage'),
            'templates' => $templates->toJson(), // Because PresentableInterface toJson() is not working in the view
        ]);
    }

    /**
     * Show the template configuration.
     *
     * @param int $template_id
     *
     * @return \Illuminate\View\View
     */
    public function show($template_id)
    {
        $template = $this->templateRepository->getById($template_id);

        return view('admin.templates.details', [
            'breadcrumb' => [
                ['url' => route('admin.templates.index'), 'label' => Lang::get('templates.label')],
            ],
            'title'        => $template->name,
            'sharedFiles'  => $template->sharedFiles,
            'configFiles'  => $template->configFiles,
            'variables'    => $template->variables,
            'project'      => $template,
            'target_type'  => 'template',
            'target_id'    => $template->id,
            'route'        => 'admin.templates.commands.step',
        ]);
    }

    /**
     * Store a newly created template in storage.
     *
     * @param StoreTemplateRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
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
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($template_id, StoreTemplateRequest $request)
    {
        return $this->templateRepository->updateById($request->only(
            'name'
        ), $template_id);
    }
}
