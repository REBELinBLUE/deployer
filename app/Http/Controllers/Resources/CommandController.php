<?php

namespace App\Http\Controllers\Resources;

use App\Command;
use App\Http\Requests\StoreCommandRequest;
use App\Project;
use App\Repositories\Contracts\CommandRepositoryInterface;
use Input;
use Lang;

/**
 * Controller for managing commands.
 */
class CommandController extends ResourceController
{
    /**
     * The group repository.
     *
     * @var CommandRepositoryInterface
     */
    private $commandRepository;

    /**
     * Class constructor.
     *
     * @param  CommandRepositoryInterface $commandRepository
     * @return void
     */
    public function __construct(CommandRepositoryInterface $commandRepository)
    {
        $this->commandRepository = $commandRepository;
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

        $project = Project::find($project_id);

        // FIXME: use a repository
        $commands = Command::where('project_id', $project->id)
                           ->with('servers')
                           ->whereIn('step', [$types[$action] - 1, $types[$action] + 1])
                           ->orderBy('order')
                           ->get();

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
            'commands'   => $commands,
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
        $command = $this->commandRepository->create($request->only(
            'name',
            'user',
            'project_id',
            'script',
            'step',
            'optional'
        ));

        $command->servers()->attach($request->servers);

        $command->servers; // Triggers the loading

        return $command;
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
        $command = $this->commandRepository->updateById($request->only(
            'name',
            'user',
            'script',
            'optional'
        ), $command_id);

        $command->servers()->sync($request->servers);

        $command->servers; // Triggers the loading

        return $command;
    }

    /**
     * Remove the specified command from storage.
     *
     * @param  int      $command_id
     * @return Response
     */
    public function destroy($command_id)
    {
        $this->commandRepository->deleteById($command_id);

        return [
            'success' => true,
        ];
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
            $server = $this->commandRepository->updateById([
                'order' => $order,
            ], $command_id);

            $order++;
        }

        return [
            'success' => true,
        ];
    }
}
