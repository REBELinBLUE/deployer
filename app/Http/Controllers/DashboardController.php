<?php namespace App\Http\Controllers;

use Lang;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\DeploymentRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
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
     * @todo Use a decorator pattern here
     */
    public function index(DeploymentRepositoryInterface $deploymentRepository, ProjectRepositoryInterface $projectRepository)
    {
        $deployments = $deploymentRepository->getTimeline();

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
            'projects' => $projectRepository->getAll()
        ]);
    }
}
