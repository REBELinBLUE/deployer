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
    public function index(
        DeploymentRepositoryInterface $deploymentRepository,
        ProjectRepositoryInterface $projectRepository
    )
    {
        $deployments = $deploymentRepository->getTimeline();
        $projects = $projectRepository->getAll();

        $deploys_by_date = [];
        foreach ($deployments as $deployment) {
            $date = $deployment->started_at->format('Y-m-d');

            if (!isset($deploys_by_date[$date])) {
                $deploys_by_date[$date] = [];
            }

            $deploys_by_date[$date][] = $deployment;
        }

        $projects_by_group = [];
        foreach ($projects as $project) {
            if (!isset($projects_by_group[$project->group->name])) {
                $projects_by_group[$project->group->name] = [];
            }

            $projects_by_group[$project->group->name][] = $project;
        }

        ksort($projects_by_group);

        return view('dashboard.index', [
            'title'    => Lang::get('dashboard.title'),
            'latest'   => $deploys_by_date,
            'projects' => $projects_by_group
        ]);
    }
}
