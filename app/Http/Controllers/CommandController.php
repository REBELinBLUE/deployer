<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Project;
use App\Command;
use App\ServerLog;

use Validator;
use Input;
use Response;

use Illuminate\Http\Request;

class CommandController extends Controller
{
    public function listing($project_id, $action)
    {
        $project = Project::findOrFail($project_id);
        
        $commands = Command::where('project_id', '=', $project->id)
                           ->where('step', 'LIKE', '%' . ucfirst($action))
                           ->orderBy('order')
                           ->get();

        // fixme: there has to be a better way to do this
        // this triggers the servers to be loaded so that they exist in the model
        foreach ($commands as $command) {
            $command->servers;
        }

        return view('commands.listing', [
            'breadcrumb'     => [
                ['url' => url('projects', $project->id), 'label' => $project->name]
            ],
            'title'          => deploy_step_label(ucfirst($action)),
            'project'        => $project,
            'action'         => $action,
            'commands'       => $commands
        ]);
    }

    public function store()
    {
        $rules = array(
            'name'       => 'required',
            'user'       => 'required',
            'script'     => 'required',
            'step'       => 'required', // FIXME: Clean this up
            'project_id' => 'required|integer'
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'errors'  => $validator->getMessageBag()->toArray()
            ], 400);
        } else {
            $command = new Command;
            $command->name       = Input::get('name');
            $command->user       = Input::get('user');
            $command->project_id = Input::get('project_id');
            $command->script     = Input::get('script');
            $command->step       = ucwords(Input::get('step'));
            $command->save();

            $command->servers()->attach(Input::get('servers'));

            return Response::json([
                'success' => true,
                'command' => $command
            ], 200);
        }
    }

    public function update($id)
    {
        $rules = array(
            'name'       => 'required',
            'user'       => 'required',
            'script'     => 'required'
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'errors'  => $validator->getMessageBag()->toArray()
            ], 400);
        } else {
            $command = Command::findOrFail($id);
            $command->name       = Input::get('name');
            $command->user       = Input::get('user');
            $command->script     = Input::get('script');
            $command->save();

            $command->servers()->detach();
            $command->servers()->attach(Input::get('servers'));

            return Response::json([
                'success' => true,
                'command' => $command
            ], 200);
        }
    }

    public function log($log_id)
    {
        $log = ServerLog::findOrFail($log_id);

        return $log;
    }
}
