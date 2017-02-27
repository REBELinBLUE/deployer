<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory as ViewFactory;
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
     * @var DeploymentRepositoryInterface
     */
    private $deploymentRepository;

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * DashboardController constructor.
     *
     * @param DeploymentRepositoryInterface $deploymentRepository
     * @param ProjectRepositoryInterface    $projectRepository
     * @param ViewFactory                   $view
     * @param Translator                    $translator
     */
    public function __construct(
        DeploymentRepositoryInterface $deploymentRepository,
        ProjectRepositoryInterface $projectRepository,
        ViewFactory $view,
        Translator $translator
    ) {
        $this->view                 = $view;
        $this->deploymentRepository = $deploymentRepository;
        $this->projectRepository    = $projectRepository;
        $this->translator           = $translator;
    }

    /**
     * The main page of the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
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

        return $this->view->make('dashboard.index', [
            'title'     => $this->translator->trans('dashboard.title'),
            'latest'    => $this->buildTimelineData(),
            'projects'  => $projects_by_group,
        ]);
    }

    /**
     * Returns the timeline.
     *
     * @return \Illuminate\View\View
     */
    public function timeline()
    {
        return $this->view->make('dashboard.timeline', [
            'latest' => $this->buildTimelineData(),
        ]);
    }

    /**
     * Generates an XML file for CCTray.
     *
     * @param ResponseFactory $response
     *
     * @return \Illuminate\View\View
     */
    public function cctray(ResponseFactory $response)
    {
        $projects = $this->projectRepository->getAll();

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
}
