<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use REBELinBLUE\Deployer\Contracts\Repositories\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\ProjectRepositoryInterface;

/**
 * The dashboard controller.
 */
class DashboardController extends Controller
{
    /**
     * The main page of the dashboard.
     *
     * @param DeploymentRepositoryInterface $deploymentRepository
     * @param ProjectRepositoryInterface    $projectRepository
     *
     * @return \Illuminate\View\View
     */
    public function index(
        DeploymentRepositoryInterface $deploymentRepository,
        ProjectRepositoryInterface $projectRepository
    ) {
        $projects = $projectRepository->getAll();

        $projects_by_group = [];
        foreach ($projects as $project) {
            if (!isset($projects_by_group[$project->group->name])) {
                $projects_by_group[$project->group->name] = [];
            }

            $projects_by_group[$project->group->name][] = $project;
        }

        ksort($projects_by_group);

        return view('dashboard.index', [
            'title'     => Lang::get('dashboard.title'),
            'latest'    => $this->buildTimelineData($deploymentRepository),
            'projects'  => $projects_by_group,
        ]);
    }

    /**
     * Returns the timeline.
     *
     * @param DeploymentRepositoryInterface $deploymentRepository
     *
     * @return \Illuminate\View\View
     */
    public function timeline(DeploymentRepositoryInterface $deploymentRepository)
    {
        return view('dashboard.timeline', [
            'latest' => $this->buildTimelineData($deploymentRepository),
        ]);
    }

    /**
     * Builds the data for the timeline.
     *
     * @param DeploymentRepositoryInterface $deploymentRepository
     *
     * @return array
     */
    private function buildTimelineData(DeploymentRepositoryInterface $deploymentRepository)
    {
        $deployments = $deploymentRepository->getTimeline();

        $deploys_by_date = [];
        foreach ($deployments as $deployment) {
            $date = $deployment->started_at->format('Y-m-d');

            if (!isset($deploys_by_date[$date])) {
                $deploys_by_date[$date] = [];
            }

            $deploys_by_date[$date][] = $deployment;
        }

        return $deploys_by_date;
    }

    /**
     * Generates an XML file for CCTray.
     *
     * @param ProjectRepositoryInterface $projectRepository
     *
     * @return \Illuminate\View\View
     */
    public function cctray(ProjectRepositoryInterface $projectRepository)
    {
        $projects = $projectRepository->getAll();

        foreach ($projects as $project) {
            $project->latest_deployment = $project->deployments->first();
        }

        return Response::view('cctray', [
            'projects' => $projects,
        ])->header('Content-Type', 'application/xml');
    }
}
