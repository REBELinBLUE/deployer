<?php namespace App\Http\Controllers;

use Input;
use Response;
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
     * @param int $project_id
     * @param string $action Either clone, install, activate or purge
     * @return Response
     */
    public function listing(Project $project, $project_id, $action)
    {
        $types = [
            'clone'    => Command::DO_CLONE,
            'install'  => Command::DO_INSTALL,
            'activate' => Command::DO_ACTIVATE,
            'purge'    => Command::DO_PURGE
        ];

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
            'title'      => deploy_step_label($action),
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
     * @todo Use mass assignment
     */
    public function store(StoreCommandRequest $request)
    {
        $max = Command::where('project_id', Input::get('project_id'))
                      ->where('step', Input::get('step'))
                      ->orderBy('order', 'desc')
                      ->first();

        $order = 0;
        if (isset($max)) {
            $order = (int) $max->order + 1;
        }

        $command = new Command;
        $command->name       = $request->name;
        $command->user       = $request->user;
        $command->project_id = $request->project_id;
        $command->script     = $request->script;
        $command->step       = $request->step;
        $command->order      = $order;
        $command->save();

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
     * @todo Use mass assignment
     * @todo Change attach/detach to sync
     */
    public function update(Command $command, StoreCommandRequest $request)
    {
        $command->name   = $request->name;
        $command->user   = $request->user;
        $command->script = $request->script;
        $command->save();

        $command->servers()->detach();
        $command->servers()->attach($request->servers);

        $command->servers; // Triggers the loading

        return $command;
    }

    /**
     * Remove the specified command from storage.
     *
     * @param int $command_id
     * @return Response
     */
    public function destroy($command_id)
    {
        $command = Command::findOrFail($command_id);
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
     * @param int $log_id
     * @param boolean $include_log
     * @return Response
     * @todo Move this to deployment controller
     */
    public function status($log_id, $include_log = false)
    {
        $log = ServerLog::findOrFail($log_id);

        $log->started  = ($log->started_at ? $log->started_at->format('g:i:s A') : null);
        $log->finished = ($log->finished_at ? $log->finished_at->format('g:i:s A') : null);
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
     * @param int $log_id
     * @return Response
     * @todo Move this to deployment controller
     */
    public function log($log_id)
    {
        return $this->status($log_id, true);
    }
}
