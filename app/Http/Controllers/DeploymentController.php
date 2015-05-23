<?php namespace App\Http\Controllers;

use Lang;
use App\Deployment;
use App\ServerLog;
use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * The controller for showing the status of deployments
 */
class DeploymentController extends Controller
{
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
}
