<?php namespace App\Http\Controllers;

use Lang;
use App\Project;
use App\Deployment;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * The dashboard controller
 */
class DashboardController extends Controller
{
    /**
     * The main page of the dashboard
     *
     * @return View
     */
    public function index()
    {
        // Get the latest 15 deployments
        $raw_sql = 'project_id IN (SELECT id FROM projects WHERE deleted_at IS NULL)';
        $deployments = Deployment::whereRaw($raw_sql) // FIXME: Surely there is a nicer way to do this?
                                 ->take(15)
                                 ->orderBy('started_at', 'DESC')
                                 ->get();

        $grouped_by_date = [];
        foreach ($deployments as $deployment) {
            $date = $deployment->started_at->format('Y-m-d');

            if (!isset($grouped_by_date[$date])) {
                $grouped_by_date[$date] = [];
            }

            $grouped_by_date[$date][] = $deployment;
        }

        return view('dashboard.index', [
            'title'    => Lang::get('dashboard.title'),
            'latest'   => $grouped_by_date,
            'projects' => Project::orderBy('name')->get()
        ]);
    }
}
