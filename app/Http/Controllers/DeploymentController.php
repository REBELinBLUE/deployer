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

        return view('deployment.details', [
            'title'      => 'Deployment Details',
            'project'    => $deployment->project,
            'deployment' => $deployment,
            'steps'      => $deployment->steps
        ]);
    }
}
