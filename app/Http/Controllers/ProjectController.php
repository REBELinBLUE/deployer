<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * The details of an individual project
     *
     * @param int $id The ID of the project to display
     * @return \Illuminate\View\View
     */
    public function details($id)
    {
        return view('project.details', [
            'title' => 'project name'
        ]);
    }
}
