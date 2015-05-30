<?php namespace App\Http\Controllers;

use Lang;
use Response;
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
     * TODO: Use a decorator pattern here
     */
    public function index(
        DeploymentRepositoryInterface $deploymentRepository,
        ProjectRepositoryInterface $projectRepository
    ) {
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

    /**
     * Generates an XML file for CCTray
     *
     * @param ProjectRepositoryInterface $projectRepository
     * @return Response
     */
    public function cctray(ProjectRepositoryInterface $projectRepository)
    {
        $projects = $projectRepository->getAll();

        foreach ($projects as $project) {
            $project->latest_deployment = $project->deployments->first();
        }

        return Response::view('cctray', [
            'projects' => $projects
        ])->header('Content-Type', 'application/xml');
    }
}
