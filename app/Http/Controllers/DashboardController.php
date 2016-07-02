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
     * @var DeploymentRepositoryInterface
     */
    private $deploymentRepository;

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @param DeploymentRepositoryInterface $deploymentRepository
     * @param ProjectRepositoryInterface $projectRepository
     */
    public function __construct(
        DeploymentRepositoryInterface $deploymentRepository,
        ProjectRepositoryInterface $projectRepository
    ) {
        $this->deploymentRepository = $deploymentRepository;
        $this->projectRepository = $projectRepository;
    }
    /**
     * The main page of the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('app');
    }

    /**
     * @return array
     */
    public function projects()
    {
        $projects = $this->projectRepository->getAll();

        $projects_by_group = [];
        foreach ($projects as $project) {
            if (!isset($projects_by_group[$project->group->name])) {
                $projects_by_group[$project->group->name] = [];
            }

            $projects_by_group[$project->group->name][] = $project;
        }

        ksort($projects_by_group);

        return [
            'title'     => Lang::get('dashboard.title'),
            'latest'    => $this->buildTimelineData(),
            'projects'  => $projects_by_group,
        ];  // dashboard.index
    }

    /**
     * Returns the timeline.
     *
     * @return \Illuminate\View\View
     */
    public function timeline()
    {
        return view('dashboard.timeline', [
            'latest' => $this->buildTimelineData(),
        ]);
    }

    /**
     * Builds the data for the timeline.
     *
     * @return array
     */
    private function buildTimelineData()
    {
        $deployments = $this->deploymentRepository->getTimeline();

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
     * @return \Illuminate\View\View
     */
    public function cctray()
    {
        $projects = $this->projectRepository->getAll();

        foreach ($projects as $project) {
            $project->latest_deployment = $project->deployments->first();
        }

        return Response::view('cctray', [
            'projects' => $projects,
        ])->header('Content-Type', 'application/xml');
    }
}
