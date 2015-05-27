<?php namespace App\Http\Controllers;

use Lang;
use Input;
use App\Project;
use App\Deployment;
use App\ServerLog;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\DeploymentRepositoryInterface;
use App\Commands\QueueDeployment;

/**
 * The controller for showing the status of deployments
 */
class DeploymentController extends Controller
{
    /**
     * The details of an individual project
     *
     * @param Project $project
     * @param DeploymentRepositoryInterface $deploymentRepository
     * @return View
     */
    public function project(Project $project, DeploymentRepositoryInterface $deploymentRepository)
    {
        $optional = $project->commands->filter(function ($command) {
            return $command->optional;
        });

        return view('projects.details', [
            'title'         => $project->name,
            'deployments'   => $deploymentRepository->getLatest($project),
            'today'         => $deploymentRepository->getTodayCount($project),
            'last_week'     => $deploymentRepository->getLastWeekCount($project),
            'project'       => $project,
            'servers'       => $project->servers,
            'notifications' => $project->notifications,
            'notifyEmails'  => $project->notifyEmails,
            'heartbeats'    => $project->heartbeats,
            'sharedFiles'   => $project->shareFiles,
            'projectFiles'  => $project->projectFiles,
            'optional'      => $optional
        ]);
    }

    /**
     * Show the deployment details
     *
     * @param Deployment $deployment
     * @return Response
     */
    public function show(Deployment $deployment)
    {
        $output = [];
        foreach ($deployment->steps as $step) {
            foreach ($step->servers as $server) {
                $server->server;

                $server->runtime  = ($server->runtime() === false ? null : human_readable_duration($server->runtime()));
                $server->output   = ((is_null($server->output) || !strlen($server->output)) ? null : '');
                $server->script   = '';
                $server->first    = (count($output) === 0); // FIXME: Let backbone.js take care of this

                $output[] = $server;
            }
        }

        $project = $deployment->project;

        return view('deployment.details', [
            'breadcrumb' => [
                ['url' => url('projects', $project->id), 'label' => $project->name]
            ],
            'title'      => Lang::get('deployments.details'),
            'project'    => $project,
            'deployment' => $deployment,
            'output'     => $output
        ]);
    }

    /**
     * Adds a deployment for the specified project to the queue
     *
     * @param Project $project
     * @return Response
     * TODO: Don't allow this to run if there is already a pending deploy or no servers
     */
    public function deploy(Project $project)
    {
        $deployment = new Deployment;
        $deployment->reason = Input::get('reason');

        if (Input::has('source') && Input::has('source_' . Input::get('source'))) {
            $deployment->branch = Input::get('source_' . Input::get('source'));
        }

        if (empty($deployment->branch)) {
            $deployment->branch = $project->branch;
        }

        $optional = [];

        if (Input::has('optional')) {
            $optional = Input::get('optional');
        }

        $this->dispatch(new QueueDeployment(
            $project,
            $deployment,
            $optional
        ));

        return redirect()->route('deployment', [
            'id' => $deployment->id
        ]);
    }
}
