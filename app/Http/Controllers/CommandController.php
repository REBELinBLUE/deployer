<?php namespace App\Http\Controllers;

use Lang;
use Input;
use App\Project;
use App\Command;
use App\ServerLog;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommandRequest;
use Illuminate\Http\Request;

/**
 * Controller for managing commands
 */
class CommandController extends Controller
{
    /**
     * Display a listing of before/after commands for the supplied stage
     *
     * @param Project $project
     * @param string $action Either clone, install, activate or purge
     * @return Response
     */
    public function listing(Project $project, $action)
    {
        $types = [
            'clone'    => Command::DO_CLONE,
            'install'  => Command::DO_INSTALL,
            'activate' => Command::DO_ACTIVATE,
            'purge'    => Command::DO_PURGE
        ];

        // fixme: use a repository
        $commands = Command::where('project_id', $project->id)
                           ->whereIn('step', array($types[$action] - 1, $types[$action] + 1))
                           ->orderBy('order')
                           ->get();

        // fixme: there has to be a better way to do this
        // this triggers the servers to be loaded so that they exist in the model
        foreach ($commands as $command) {
            $command->servers;
        }

        return view('commands.listing', [
            'breadcrumb' => [
                ['url' => url('projects', $project->id), 'label' => $project->name]
            ],
            'title'      => Lang::get('commands.' . strtolower($action)),
            'project'    => $project,
            'action'     => $types[$action],
            'commands'   => $commands
        ]);
    }

    /**
     * Store a newly created command in storage.
     *
     * @param StoreCommandRequest $request
     * @return Response
     */
    public function store(StoreCommandRequest $request)
    {
        // fixme: use a repository
        $max = Command::where('project_id', $request->project_id)
                      ->where('step', $request->step)
                      ->orderBy('order', 'desc')
                      ->first();

        $order = 0;
        if (isset($max)) {
            $order = $max->order + 1;
        }

        $fields = $request->only(
            'name',
            'user',
            'project_id',
            'script',
            'step',
            'optional'
        );

        $fields['order'] = $order;

        $command = Command::create($fields);

        $command->servers()->attach($request->servers);

        $command->servers; // Triggers the loading

        return $command;
    }

    /**
     * Update the specified command in storage.
     *
     * @param Command $command
     * @param StoreCommandRequest $request
     * @return Response
     */
    public function update(Command $command, StoreCommandRequest $request)
    {
        $command->update($request->only(
            'name',
            'user',
            'script',
            'optional'
        ));

        $command->save();

        $command->servers()->sync($request->servers);

        $command->servers; // Triggers the loading

        return $command;
    }

    /**
     * Remove the specified command from storage.
     *
     * @param Command $command
     * @return Response
     */
    public function destroy(Command $command)
    {
        $command->delete();

        return [
            'success' => true
        ];
    }

    /**
     * Re-generates the order for the supplied commands
     *
     * @return Response
     */
    public function reorder()
    {
        $order = 0;

        foreach (Input::get('commands') as $command_id) {
            $command = Command::findOrFail($command_id);

            $command->order = $order;

            $command->save();

            $order++;
        }

        return [
            'success' => true
        ];
    }

    /**
     * Gets the status of a particular deployment step
     *
     * @param ServerLog $log
     * @param boolean $include_log
     * @return Response
     * TODO: Move this to deployment controller
     */
    public function status(ServerLog $log, $include_log = false)
    {
        $log->runtime  = ($log->runtime() === false ? null : human_readable_duration($log->runtime()));
        $log->script   = '';

        if (!$include_log) {
            $log->output = ((is_null($log->output) || !strlen($log->output)) ? null : '');
        }

        return $log;
    }

    /**
     * Gets the log output of a particular deployment step
     *
     * @param ServerLog $log
     * @return Response
     * TODO: Move this to deployment controller
     */
    public function log(ServerLog $log)
    {
        return $this->status($log, true);
    }
}
