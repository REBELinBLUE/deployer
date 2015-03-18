<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Project;
use App\Deployment;
use App\Commands\DeployProject;

use Illuminate\Http\Request;
use Queue;

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

        // FIXME: Check if a deployment is already in progress

        $deployment = new Deployment;
        $deployment->run = date('Y-m-d H:i:s');
        $deployment->project_id = $project_id;
        $deployment->user_id = 1; // FIXME: Get logged in user
        $deployment->committer = 'Loading'; // FIXME: Better values for these
        $deployment->commit = 'Loading';
        $deployment->save();

        $deployment->project->status = 'Running';
        $deployment->project->save();

        Queue::push(new DeployProject($deployment));

        return view('project.deploy', [
            'title'      => 'Deploying project....',
            'project'    => $project,
            'deployment' => $deployment
        ]);
    }

    public function commands($project_id, $command)
    {
        return view('project.commands', [
            'title'   => 'project ' . $project_id . ' command ' . $command,
            'command' => $command
        ]);
    }
}
