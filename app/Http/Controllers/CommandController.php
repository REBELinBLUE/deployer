<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Project;
use App\Command;
use App\ServerLog;

use Illuminate\Http\Request;

class CommandController extends Controller
{
    public function listing($project_id, $command)
    {
        $project = Project::findOrFail($project_id);

        // FIXME: Refactor this
        $before = Command::where('project_id', '=', $project->id)
                         ->where('step', '=', 'Before ' . ucfirst($command))
                         ->orderBy('order')
                         ->get();

        $after = Command::where('project_id', '=', $project->id)
                        ->where('step', '=', 'After ' . ucfirst($command))
                        ->orderBy('order')
                        ->get();

        return view('project.commands', [
            'title'   => deploy_step_label(ucfirst($command)),
            'command' => $command,
            'before'  => $before,
            'after'   => $after
        ]);
    }

    public function log($log_id)
    {
        $log = ServerLog::findOrFail($log_id);

        return $log;
    }
}
