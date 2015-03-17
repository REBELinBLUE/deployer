<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Project;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * The main page of the dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('dashboard.index', [
            'title'        => 'Dashboard',
            'projects'     => Project::orderBy('name')->get(),
            'is_dashboard' => true
        ]);
    }
}
