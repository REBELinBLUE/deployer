<?php

namespace App\Http\Controllers\Resources;

use App\Command;
use App\Http\Requests\StoreCommandRequest;
use App\Repositories\Contracts\CommandRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Input;
use Lang;

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
     * @return Response
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
            ['url' => url('projects', $project->id), 'label' => $project->name],
        ];

        if ($project->is_template) {
            $breadcrumb = [
                ['url' => url('admin/templates'), 'label' => Lang::get('templates.label')],
                ['url' => url('admin/templates', $project->id), 'label' => $project->name],
            ];
        }

        return view('commands.listing', [
            'breadcrumb' => $breadcrumb,
            'title'      => Lang::get('commands.' . strtolower($action)),
            'project'    => $project,
            'action'     => $types[$action],
            'commands'   => $this->repository->getForDeployStep($project->id, $types[$action]),
        ]);
    }

    /**
     * Store a newly created command in storage.
     *
     * @param  StoreCommandRequest $request
     * @return Response
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
            'servers'
        ));
    }

    /**
     * Update the specified command in storage.
     *
     * @param  int                 $command_id
     * @param  StoreCommandRequest $request
     * @return Response
     */
    public function update($command_id, StoreCommandRequest $request)
    {
        return $this->repository->updateById($request->only(
            'name',
            'user',
            'script',
            'optional',
            'servers'
        ), $command_id);
    }

    /**
     * Re-generates the order for the supplied commands.
     *
     * @return Response
     */
    public function reorder()
    {
        $order = 0;

        foreach (Input::get('commands') as $command_id) {
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
