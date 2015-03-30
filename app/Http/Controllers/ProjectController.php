<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Project;
use App\Deployment;

use Validator;
use Input;
use Response;

use App\Commands\QueueDeployment;

use Carbon\Carbon;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * The details of an individual project
     *
     * @param int $project_id The ID of the project to display
     * @return \Illuminate\View\View
     */
    public function show($project_id)
    {
        $project = Project::findOrFail($project_id);

        $commands = [
            'clone'     => null,
            'install'   => null,
            'activate'  => null,
            'purge'     => null
        ];

        foreach ($project->commands as $command) {
            // FIXME: There has to be a cleaner way to do this surely? Maybe on the model
            $steps  = explode(' ', $command->step);
            $action = strtolower($steps[1]);
            $when   =  strtolower($steps[0]);

            if (!is_array($commands[$action])) {
                $commands[$action] = [];
            }

            if (!isset($commands[$action][$when])) {
                $commands[$action][$when] = [];
            }

            $commands[$action][$when][] = $command->name;
        }

        $deployments = Deployment::where('project_id', '=', $project->id)
                                 ->take($project->builds_to_keep)
                                 ->orderBy('started_at', 'DESC')
                                 ->get();

        $now = Carbon::now();
        $lastWeek = Carbon::now()->subWeek();
        $yesterday = Carbon::now()->yesterday();

        $today = Deployment::where('project_id', '=', $project->id)
                           ->where('started_at', '>=', $now->format('Y-m-d') . ' 00:00:00')
                           ->where('started_at', '<=', $now->format('Y-m-d') . ' 23:59:59')
                           ->count();

        $week = Deployment::where('project_id', '=', $project->id)
                          ->where('started_at', '>=', $lastWeek->format('Y-m-d') . ' 00:00:00')
                          ->where('started_at', '<=', $yesterday->format('Y-m-d') . ' 23:59:59')
                          ->count();

        return view('project.details', [
            'title'       => $project->name,
            'deployments' => $deployments,
            'today'       => $today,
            'last_week'   => $week,
            'project'     => $project,
            'servers'     => $project->servers, // Order by name
            'commands'    => $commands
        ]);
    }

    /**
     * FIXME: Don't allow this to run if there is already a pending deploy or no servers
     */
    public function deploy($project_id)
    {
        $project = Project::findOrFail($project_id);
        $deployment = new Deployment;

        $this->dispatch(new QueueDeployment($project, $deployment));

        return redirect()->route('deployment', [
            'id' => $deployment->id
        ]);
    }

    public function store()
    {
        $rules = array(
            'name'           => 'required',
            'repository'     => 'required',
            'branch'         => 'required',
            'builds_to_keep' => 'required|integer|min:1|max:20',
            'url'            => 'url',
            'build_url'      => 'url'
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'errors'  => $validator->getMessageBag()->toArray()
            ], 400);
        } else {
            $project = new Project;
            $project->name           = Input::get('name');
            $project->repository     = Input::get('repository');
            $project->branch         = Input::get('branch');
            $project->builds_to_keep = Input::get('builds_to_keep');
            $project->url            = Input::get('url');
            $project->build_url      = Input::get('build_url');

            $project->generateSSHKey();
            $project->generateHash();
            $project->save();

            return Response::json([
                'success' => true,
                'project' => $project,
                'redirect' => url('projects', $project->id)
            ], 200);
        }
    }

    public function update($id)
    {
        $rules = array(
            'name'           => 'required',
            'repository'     => 'required',
            'branch'         => 'required',
            'builds_to_keep' => 'required|integer|min:1|max:20',
            'url'            => 'url',
            'build_url'      => 'url'
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'errors'  => $validator->getMessageBag()->toArray()
            ], 400);
        } else {
            $project = Project::findOrFail($id);
            $project->name           = Input::get('name');
            $project->repository     = Input::get('repository');
            $project->branch         = Input::get('branch');
            $project->builds_to_keep = Input::get('builds_to_keep');
            $project->url            = Input::get('url');
            $project->build_url      = Input::get('build_url');

            $project->save();

            return Response::json([
                'success' => true,
                'project' => $project
            ], 200);
        }
    }


    public function servers($project)
    {
        $project = Project::findOrFail($project);

        return $project->servers;
    }

}
