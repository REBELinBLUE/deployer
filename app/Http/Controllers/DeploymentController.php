<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Deployment;

use Illuminate\Http\Request;

class DeploymentController extends Controller
{
    public function show($deployment_id)
    {
        $deployment = Deployment::findOrFail($deployment_id);

        $project = $deployment->project;

        return view('deployment.details', [
            'breadcrumb' => [
                ['url' => route('project', $project->id), 'label' => $project->name]
            ],
            'title'      => 'Deployment Details',
            'project'    => $project,
            'deployment' => $deployment,
            'steps'      => $deployment->steps
        ]);
    }
}
