<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;

/**
 * The dashboard controller.
 */
class DashboardController extends Controller
{
    /**
     * @var ViewFactory
     */
    private $view;

    /**
     * DashboardController constructor.
     *
     * @param ViewFactory $view
     */
    public function __construct(ViewFactory $view)
    {
        $this->view = $view;
    }

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

        return $this->view->make('dashboard.index', [
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
        return $this->view->make('dashboard.timeline', [
            'latest' => $this->buildTimelineData($deploymentRepository),
        ]);
    }

    /**
     * Generates an XML file for CCTray.
     *
     * @param ProjectRepositoryInterface $projectRepository
     * @param ResponseFactory            $response
     *
     * @return \Illuminate\View\View
     */
    public function cctray(ProjectRepositoryInterface $projectRepository, ResponseFactory $response)
    {
        $projects = $projectRepository->getAll();

        foreach ($projects as $project) {
            $project->latest_deployment = $project->deployments->first();
        }

        return $response->view('cctray', [
            'projects' => $projects,
        ])->header('Content-Type', 'application/xml');
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
}
