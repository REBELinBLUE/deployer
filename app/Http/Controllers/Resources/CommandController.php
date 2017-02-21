<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Http\Requests\StoreCommandRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\CommandRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for managing commands.
 */
class CommandController extends Controller
{
    use ResourceController;

    /**
     * The project repository.
     *
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * CommandController constructor.
     *
     * @param CommandRepositoryInterface $commandRepository
     * @param ProjectRepositoryInterface $projectRepository
     * @param ViewFactory                $view
     * @param Translator                 $translator
     */
    public function __construct(
        CommandRepositoryInterface $commandRepository,
        ProjectRepositoryInterface $projectRepository
    ) {
        $this->repository        = $commandRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * Display a listing of before/after commands for the supplied stage.
     *
     * @param int          $target_id
     * @param int          $action
     * @param Translator   $translator
     * @param ViewFactory  $view
     * @param UrlGenerator $url
     *
     * @return \Illuminate\View\View
     */
    public function listing($target_id, $action, Translator $translator, ViewFactory $view, UrlGenerator $url)
    {
        $types = [
            'clone'    => Command::DO_CLONE,
            'install'  => Command::DO_INSTALL,
            'activate' => Command::DO_ACTIVATE,
            'purge'    => Command::DO_PURGE,
        ];

        $project = $this->projectRepository->getById($target_id);
        $target  = 'project';

        $breadcrumb = [
            ['url' => $url->route('projects', ['id' => $project->id]), 'label' => $project->name],
        ];

        return $view->make('commands.listing', [
            'breadcrumb'  => $breadcrumb,
            'title'       => $translator->trans('commands.' . strtolower($action)),
            'subtitle'    => $project->name,
            'project'     => $project,
            'target_type' => $target,
            'target_id'   => $project->id,
            'action'      => $types[$action],
            'commands'    => $this->repository->getForDeployStep($project->id, $target, $types[$action]),
        ]);
    }

    /**
     * Store a newly created command in storage.
     *
     * @param StoreCommandRequest $request
     * @param ResponseFactory     $response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCommandRequest $request, ResponseFactory $response)
    {
        return $response->json($this->repository->create($request->only(
            'name',
            'user',
            'target_type',
            'target_id',
            'script',
            'step',
            'optional',
            'default_on',
            'servers'
        )), Response::HTTP_CREATED);
    }

    /**
     * Update the specified command in storage.
     *
     * @param int                 $command_id
     * @param StoreCommandRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($command_id, StoreCommandRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'user',
            'script',
            'optional',
            'default_on',
            'servers'
        ), $command_id);
    }

    /**
     * Re-generates the order for the supplied commands.
     *
     * @param Request $request
     *
     * @return array
     */
    public function reorder(Request $request)
    {
        $order = 0;

        foreach ($request->get('commands') as $command_id) {
            $this->repository->updateById([
                'order' => $order,
            ], $command_id);

            $order++;
        }

        return [
            'success' => true,
        ];
    }
}
