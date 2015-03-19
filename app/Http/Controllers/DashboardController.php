<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Project;
use App\Deployment;

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
        $latest = [];
        foreach (Deployment::take(15)->orderBy('started_at', 'DESC')->get() as $deployment) {
            $date = $deployment->started_at->format('Y-m-d');

            if (!isset($latest[$date])) {
                $latest[$date] = [];
            }

            $latest[$date][] = $deployment;
        }

        return view('dashboard.index', [
            'title'        => 'Dashboard',
            'latest'       => $latest,
            'projects'     => Project::orderBy('name')->get(),
            'is_dashboard' => true
        ]);
    }
}
