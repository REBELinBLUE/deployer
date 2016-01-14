<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\ServerLog;

/**
 * The controller for showing the status of deployments.
 */
class DeploymentController extends Controller
{
    /**
     * The project repository.
     *
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * The deployment repository.
     *
     * @var deploymentRepository
     */
    private $deploymentRepository;

    /**
     * Class constructor.
     *
     * @param  ProjectRepositoryInterface    $projectRepository
     * @param  DeploymentRepositoryInterface $projectRepository
     * @return void
     */
    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        DeploymentRepositoryInterface $deploymentRepository
    ) {
        $this->projectRepository    = $projectRepository;
        $this->deploymentRepository = $deploymentRepository;
    }

    /**
     * The details of an individual project.
     *
     * @param  int                           $project_id
     * @param  DeploymentRepositoryInterface $deploymentRepository
     * @return View
     */
    public function project($project_id)
    {
        $project = $this->projectRepository->getById($project_id);

        $optional = $project->commands->filter(function (Command $command) {
            return $command->optional;
        });

        return view('projects.details', [
            'title'         => $project->name,
            'deployments'   => $this->deploymentRepository->getLatest($project_id),
            'today'         => $this->deploymentRepository->getTodayCount($project_id),
            'last_week'     => $this->deploymentRepository->getLastWeekCount($project_id),
            'project'       => $project,
            'servers'       => $project->servers,
            'notifications' => $project->notifications,
            'notifyEmails'  => $project->notifyEmails,
            'heartbeats'    => $project->heartbeats,
            'sharedFiles'   => $project->sharedFiles,
            'projectFiles'  => $project->projectFiles,
            'checkUrls'     => $project->checkUrls,
            'variables'     => $project->variables,
            'optional'      => $optional,
            'route'         => 'commands',
        ]);
    }

    /**
     * Show the deployment details.
     *
     * @param  int      $deployment
     * @return Response
     */
    public function show($deployment_id)
    {
        $deployment = $this->deploymentRepository->getById($deployment_id);

        $output = [];
        foreach ($deployment->steps as $step) {
            foreach ($step->servers as $server) {
                $server->server;

                $server->runtime = ($server->runtime() === false ? null : $server->getPresenter()->readable_runtime);
                $server->output  = ((is_null($server->output) || !strlen($server->output)) ? null : '');

                $output[] = $server;
            }
        }

        $project = $deployment->project;

        return view('deployment.details', [
            'breadcrumb' => [
                ['url' => url('projects', $project->id), 'label' => $project->name],
            ],
            'title'      => Lang::get('deployments.deployment_number', ['id' => $deployment->id]),
            'subtitle'   => $project->name,
            'project'    => $project,
            'deployment' => $deployment,
            'output'     => json_encode($output), // PresentableInterface does not correctly json encode the models
        ]);
    }

    /**
     * Adds a deployment for the specified project to the queue.
     *
     * @param  int      $project
     * @return Response
     */
    public function deploy($project_id)
    {
        $project = $this->projectRepository->getById($project_id);

        if ($project->servers->where('deploy_code', true)->count() === 0) {
            return redirect()->url('projects', $project->id);
        }

        $data = [
            'reason'     => Input::get('reason'),
            'project_id' => $project->id,
            'branch'     => $project->branch,
            'optional'   => [],
        ];

        // If allow other branches is set, check for post data
        if ($project->allow_other_branch) {
            if (Input::has('source') && Input::has('source_' . Input::get('source'))) {
                $data['branch'] = Input::get('source_' . Input::get('source'));
            }
        }

        if (Input::has('optional') && is_array(Input::get('optional'))) {
            // TODO: See if this can be removed when switching to the $request class as it use to work!
            $data['optional'] = array_map('intval', Input::get('optional'));
        }

        $deployment = $this->deploymentRepository->create($data);

        return redirect()->route('deployment', [
            'id' => $deployment->id,
        ]);
    }

    /**
     * Abort a deployment.
     *
     * @param  int      $deployment_id
     * @return Response
     */
    public function abort($deployment_id)
    {
        $this->deploymentRepository->abort($deployment_id);

        return redirect()->route('deployment', [
            'id' => $deployment_id,
        ]);
    }

    /**
     * Gets the log output of a particular deployment step.
     *
     * @param  ServerLog $log
     * @return ServerLog
     */
    public function log(ServerLog $log)
    {
        $log->runtime = ($log->runtime() === false ? null : $log->getPresenter()->readable_runtime);

        return $log;
    }
}
