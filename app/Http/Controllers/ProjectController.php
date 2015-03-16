<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
        return view('project.details', [
            'title'              => 'project ' . $project_id,
            'is_project_details' => true
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
