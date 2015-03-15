<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function details()
    {
        return view('project.details');
    }
}
