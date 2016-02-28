<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Contracts\Repositories\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\ProjectRepositoryInterface;
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
     * @var DeploymentRepositoryInterface
     */
    private $deploymentRepository;

    /**
     * DeploymentController constructor.
     *
     * @param ProjectRepositoryInterface $projectRepository
     * @param DeploymentRepositoryInterface $deploymentRepository
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
     * @param int $project_id
     *
     * @return \Illuminate\View\View
     */
    public function project($project_id)
    {
        $project = $this->projectRepository->getById($project_id);

        $this->authorize('view', $project);

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
            'configFiles'   => $project->configFiles,
            'checkUrls'     => $project->checkUrls,
            'variables'     => $project->variables,
            'optional'      => $optional,
            'tags'          => $project->tags()->reverse(),
            'branches'      => $project->branches(),
            'route'         => 'commands.step',
            'target_type'   => 'project',
            'target_id'     => $project->id,
        ]);
    }

    /**
     * Show the deployment details.
     *
     * @param int $deployment_id
     *
     * @return \Illuminate\View\View
     */
    public function show($deployment_id)
    {
        $deployment = $this->deploymentRepository->getById($deployment_id);

        $this->authorize('view', $deployment->project);

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
                ['url' => route('projects', ['id' => $project->id]), 'label' => $project->name],
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
     * @param Request $request
     * @param int $project_id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deploy(Request $request, $project_id)
    {
        $project = $this->projectRepository->getById($project_id);

        $this->authorize('deplou', $project);

        if ($project->servers->where('deploy_code', true)->count() === 0) {
            return redirect()->route('projects', ['id' => $project->id]);
        }

        $data = [
            'reason'     => $request->get('reason'),
            'project_id' => $project->id,
            'branch'     => $project->branch,
            'optional'   => [],
        ];

        // If allow other branches is set, check for post data
        if ($project->allow_other_branch) {
            if ($request->has('source') && $request->has('source_' . $request->get('source'))) {
                $data['branch'] = $request->get('source_' . $request->get('source'));
            }
        }

        // Get the optional commands and typecast to integers
        if ($request->has('optional') && is_array($request->get('optional'))) {
            $data['optional'] = array_filter(array_map(function ($value) {
                return filter_var($value, FILTER_VALIDATE_INT);
            }, $request->get('optional')));
        }

        $deployment = $this->deploymentRepository->create($data);

        return redirect()->route('deployments', [
            'id' => $deployment->id,
        ]);
    }

    /**
     * Loads a previous deployment and then creates a new deployment based on it.
     *
     * @param Request $request
     * @param int $deployment_id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rollback(Request $request, $deployment_id)
    {
        $optional = [];

        // Get the optional commands and typecast to integers
        if ($request->has('optional') && is_array($request->get('optional'))) {
            $optional = array_filter(array_map(function ($value) {
                return filter_var($value, FILTER_VALIDATE_INT);
            }, $request->get('optional')));
        }

        $deployment = $this->deploymentRepository->rollback($deployment_id, $optional);

        return redirect()->route('deployments', [
            'id' => $deployment->id,
        ]);
    }

    /**
     * Abort a deployment.
     *
     * @param int $deployment_id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function abort($deployment_id)
    {
        $deployment = $this->deploymentRepository->abort($deployment_id);

        $this->authorize('deploy', $deployment->project);

        return redirect()->route('deployment', [
            'id' => $deployment->id,
        ]);
    }

    /**
     * Gets the log output of a particular deployment step.
     *
     * @param ServerLog $log
     *
     * @return ServerLog
     */
    public function log(ServerLog $log)
    {
        $log->runtime = ($log->runtime() === false ? null : $log->getPresenter()->readable_runtime);

        return $log;
    }
}
