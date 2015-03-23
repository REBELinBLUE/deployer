<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Deployment;
use App\ServerLog;

use Request;
use Response;

class DeploymentController extends Controller
{
    public function show($deployment_id)
    {
        $deployment = Deployment::findOrFail($deployment_id);

        $project = $deployment->project;
        $output = [];
        foreach ($deployment->steps as $step) {
            foreach ($step->servers as $server) {
                $server->server;

                $server->started = ($server->started_at ? $server->started_at->format('g:i:s A') : null);
                $server->finished = ($server->finished_at ? $server->finished_at->format('g:i:s A') : null);
                $server->runtime = ($server->runtime() === false ? null : human_readable_duration($server->runtime()));

                $output[] = $server;
            }
        }

        return view('deployment.details', [
            'breadcrumb' => [
                ['url' => url('projects', $project->id), 'label' => $project->name]
            ],
            'title'      => 'Deployment Details',
            'project'    => $project,
            'deployment' => $deployment,
            'output'     => $output
        ]);
    }

    public function status($log_id)
    {
        $log = ServerLog::findOrFail($log_id);

        $log->server;

        $log->started = ($log->started_at ? $log->started_at->format('g:i:s A') : null);
        $log->finished = ($log->finished_at ? $log->finished_at->format('g:i:s A') : null);
        $log->runtime = ($log->runtime() === false ? null : human_readable_duration($log->runtime()));

        return $log;
    }
}
