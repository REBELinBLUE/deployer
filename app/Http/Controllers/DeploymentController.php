<?php namespace App\Http\Controllers;

use Lang;
use App\Deployment;
use App\ServerLog;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class DeploymentController extends Controller
{
    public function show($deployment_id)
    {
        $deployment = Deployment::findOrFail($deployment_id);

        $output = [];
        foreach ($deployment->steps as $step) {
            foreach ($step->servers as $server) {
                $server->server;

                $server->started  = ($server->started_at ? $server->started_at->format('g:i:s A') : null);
                $server->finished = ($server->finished_at ? $server->finished_at->format('g:i:s A') : null);
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
