<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Project;
use App\Deployment;
use App\ServerLog;
use App\Command;
use App\Commands\QueueDeployment;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * The details of an individual project
     *
     * @param int $project_id The ID of the project to display
     * @return \Illuminate\View\View
     */
    public function details($project_id)
    {
        $project = Project::find($project_id);

        return view('project.details', [
            'title'              => $project->name,
            'deployments'        => $project->deployments, // Get only the last x?
            'project'            => $project,
            'servers'            => $project->servers, // Order by name
            'is_project_details' => true
        ]);
    }

    public function deploy($project_id)
    {
        $project = Project::findOrFail($project_id);
        $deployment = new Deployment;

        $this->dispatch(new QueueDeployment($project, $deployment));

        return view('project.deploy', [
            'title'      => 'Deploying project....',
            'project'    => $project,
            'deployment' => $deployment,
            'steps'      => $deployment->steps
        ]);
    }

    public function deployment($project_id, $deployment_id)
    {
        $project = Project::findOrFail($project_id);
        $deployment = Deployment::findOrFail($deployment_id);

        return view('project.deploy', [
            'title'      => 'Deployment Details',
            'project'    => $project,
            'deployment' => $deployment,
            'steps'      => $deployment->steps
        ]);
    }

    public function commands($project_id, $command)
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

    /**
     * TODO Check for input, make sure it is a valid gitlab hook, check repo and branch are correct
     * http://doc.gitlab.com/ee/web_hooks/web_hooks.html
     */
    public function webhook($hash)
    {
        $project = Project::where('hash', '=', $hash)->first();

        $success = false;
        if (!is_null($project)) {
            $this->dispatch(new QueueDeployment($project, new Deployment));

            $success = true;
        }

        return [
            'success' => $success
        ];
    }

    public function log($log_id)
    {
        $log = ServerLog::findOrFail($log_id);

        return $log;
    }
}
