<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Contracts\Repositories\CommandRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Http\Requests\StoreCommandRequest;

/**
 * Controller for managing commands.
 */
class CommandController extends ResourceController
{
    /**
     * The project repository.
     *
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * Class constructor.
     *
     * @param  CommandRepositoryInterface $commandRepository
     * @return void
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
     * @param  int      $project_id
     * @param  string   $action     Either clone, install, activate or purge
     * @return \Illuminate\View\View
     */
    public function listing($project_id, $action)
    {
        $types = [
            'clone'    => Command::DO_CLONE,
            'install'  => Command::DO_INSTALL,
            'activate' => Command::DO_ACTIVATE,
            'purge'    => Command::DO_PURGE,
        ];

        $project = $this->projectRepository->getById($project_id);

        $breadcrumb = [
            ['url' => route('projects', ['id' => $project->id]), 'label' => $project->name],
        ];

        if ($project->is_template) {
            $breadcrumb = [
                ['url' => route('admin.templates.index'), 'label' => Lang::get('templates.label')],
                ['url' => route('admin.templates.show', ['templates' => $project->id]), 'label' => $project->name],
            ];
        }

        return view('commands.listing', [
            'breadcrumb' => $breadcrumb,
            'title'      => Lang::get('commands.' . strtolower($action)),
            'subtitle'   => $project->name,
            'project'    => $project,
            'action'     => $types[$action],
            'commands'   => $this->repository->getForDeployStep($project->id, $types[$action]),
        ]);
    }

    /**
     * Store a newly created command in storage.
     *
     * @param  StoreCommandRequest $request
     * @return \Illuminate\View\View
     */
    public function store(StoreCommandRequest $request)
    {
        return $this->repository->create($request->only(
            'name',
            'user',
            'project_id',
            'script',
            'step',
            'optional',
            'default_on',
            'servers'
        ));
    }

    /**
     * Update the specified command in storage.
     *
     * @param  int                 $command_id
     * @param  StoreCommandRequest $request
     * @return \Illuminate\View\View
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
     * @param  Request  $request
     * @return \Illuminate\View\View
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
