<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Support\Facades\Response;
use REBELinBLUE\Deployer\Contracts\Repositories\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\GroupRepositoryInterface;
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
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @param DeploymentRepositoryInterface $deploymentRepository
     * @param ProjectRepositoryInterface $projectRepository
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(
        DeploymentRepositoryInterface $deploymentRepository,
        ProjectRepositoryInterface $projectRepository,
        GroupRepositoryInterface $groupRepository
    ) {
        $this->deploymentRepository = $deploymentRepository;
        $this->projectRepository = $projectRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('app', [
            'latest' => json_encode($this->buildTimelineData(), JSON_FORCE_OBJECT),
            'projects' => json_encode($this->projectRepository->getAll()),
            'groups' => json_encode($this->groupRepository->getAll()),
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
