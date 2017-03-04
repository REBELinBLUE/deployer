<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController;
use REBELinBLUE\Deployer\Http\Requests\StoreTemplateRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\CommandRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for managing deployment template.
 */
class TemplateController extends Controller
{
    use ResourceController;

    /**
     * The template repository.
     *
     * @var TemplateRepositoryInterface
     */
    private $templateRepository;

    /**
     * @var ViewFactory
     */
    private $view;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var UrlGenerator
     */
    private $url;

    /**
     * TemplateController constructor.
     *
     * @param CommandRepositoryInterface  $commandRepository
     * @param TemplateRepositoryInterface $templateRepository
     * @param ViewFactory                 $view
     * @param Translator                  $translator
     * @param UrlGenerator                $url
     */
    public function __construct(
        CommandRepositoryInterface $commandRepository,
        TemplateRepositoryInterface $templateRepository,
        ViewFactory $view,
        Translator $translator,
        UrlGenerator $url
    ) {
        $this->repository         = $commandRepository;
        $this->templateRepository = $templateRepository;
        $this->view               = $view;
        $this->translator         = $translator;
        $this->url                = $url;
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
        $target   = 'template';

        $breadcrumb = [
            [
                'url'   => $this->url->route('admin.templates.index'),
                'label' => $this->translator->trans('templates.label'),
            ],
            [
                'url'   => $this->url->route('admin.templates.show', ['templates' => $template->id]),
                'label' => $template->name,
            ],
        ];

        return $this->view->make('commands.listing', [
            'breadcrumb'  => $breadcrumb,
            'title'       => $this->translator->trans('commands.' . strtolower($action)),
            'subtitle'    => $template->name,
            'project'     => $template, // FIXME: Name this to 'target'
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

        return $this->view->make('admin.templates.listing', [
            'title'     => $this->translator->trans('templates.manage'),
            'templates' => $templates->toJson(),
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

        return $this->view->make('admin.templates.details', [
            'breadcrumb' => [
                [
                    'url'   => $this->url->route('admin.templates.index'),
                    'label' => $this->translator->trans('templates.label'),
                ],
            ],
            'title'        => $template->name,
            'sharedFiles'  => $template->sharedFiles,
            'configFiles'  => $template->configFiles,
            'variables'    => $template->variables,
            'project'      => $template, // fixme: change to target
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
     * @param  ResponseFactory                     $response
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(StoreTemplateRequest $request, ResponseFactory $response)
    {
        return $response->json($this->templateRepository->create($request->only(
            'name'
        )), Response::HTTP_CREATED);
    }

    /**
     * Update the specified template in storage.
     *
     * @param int                  $template_id
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
