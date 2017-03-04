<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ServerLogRepositoryInterface;
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
     * @var ViewFactory
     */
    private $view;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var Redirector
     */
    private $redirect;

    /**
     * DeploymentController constructor.
     *
     * @param ProjectRepositoryInterface    $projectRepository
     * @param DeploymentRepositoryInterface $deploymentRepository
     * @param ViewFactory                   $view
     * @param Translator                    $translator
     * @param Redirector                    $redirect
     */
    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        DeploymentRepositoryInterface $deploymentRepository,
        ViewFactory $view,
        Translator $translator,
        Redirector $redirect
    ) {
        $this->projectRepository    = $projectRepository;
        $this->deploymentRepository = $deploymentRepository;
        $this->view                 = $view;
        $this->translator           = $translator;
        $this->redirect             = $redirect;
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

        $optional = $project->commands->filter(function (Command $command) {
            return $command->optional;
        });

        return $this->view->make('projects.details', [
            'title'         => $project->name,
            'deployments'   => $this->deploymentRepository->getLatest($project_id),
            'today'         => $this->deploymentRepository->getTodayCount($project_id),
            'last_week'     => $this->deploymentRepository->getLastWeekCount($project_id),
            'project'       => $project,
            'servers'       => $project->servers,
            'channels'      => $project->channels,
            'heartbeats'    => $project->heartbeats,
            'sharedFiles'   => $project->sharedFiles,
            'configFiles'   => $project->configFiles,
            'checkUrls'     => $project->checkUrls,
            'variables'     => $project->variables,
            'optional'      => $optional,
            'tags'          => $project->tags,
            'branches'      => $project->branches,
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
     * @param  UrlGenerator          $url
     * @return \Illuminate\View\View
     */
    public function show($deployment_id, UrlGenerator $url)
    {
        $deployment = $this->deploymentRepository->getById($deployment_id);

        $output = new Collection();
        foreach ($deployment->steps as $step) {
            foreach ($step->servers as $server) {
                $server->server;

                $server->runtime = ($server->runtime() === false ? null : $server->getPresenter()->readable_runtime);
                $server->output  = ((is_null($server->output) || !strlen($server->output)) ? null : '');

                $output->push($server);
            }
        }

        $project = $deployment->project;

        return $this->view->make('deployment.details', [
            'breadcrumb' => [
                ['url' => $url->route('projects', ['id' => $project->id]), 'label' => $project->name],
            ],
            'title'      => $this->translator->trans('deployments.deployment_number', ['id' => $deployment->id]),
            'subtitle'   => $project->name,
            'project'    => $project,
            'deployment' => $deployment,
            'output'     => $output->toJson(),
        ]);
    }

    /**
     * Adds a deployment for the specified project to the queue.
     *
     * @param Request $request
     * @param int     $project_id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deploy(Request $request, $project_id)
    {
        $project = $this->projectRepository->getById($project_id);

        if ($project->servers->where('deploy_code', true)->count() === 0) {
            return $this->redirect->route('projects', ['id' => $project->id]);
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

        return $this->redirect->route('deployments', [
            'id' => $deployment->id,
        ]);
    }

    /**
     * Queue a project to have the git mirror updated.
     *
     * @param int $project_id
     *
     * @return array
     */
    public function refresh($project_id)
    {
        $this->projectRepository->refreshBranches($project_id);

        return [
            'success' => true,
        ];
    }

    /**
     * Loads a previous deployment and then creates a new deployment based on it.
     *
     * @param Request $request
     * @param int     $deployment_id
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

        $deployment = $this->deploymentRepository->rollback($deployment_id, $request->get('reason'), $optional);

        return $this->redirect->route('deployments', [
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
        $this->deploymentRepository->abort($deployment_id);

        return $this->redirect->route('deployments', [
            'id' => $deployment_id,
        ]);
    }

    /**
     * Gets the log output of a particular deployment step.
     *
     * @param int                          $log_id
     * @param ServerLogRepositoryInterface $repository
     *
     * @return ServerLog
     */
    public function log($log_id, ServerLogRepositoryInterface $repository)
    {
        $log          = $repository->getById($log_id);
        $log->runtime = ($log->runtime() === false ? null : $log->getPresenter()->readable_runtime);

        return $log;
    }
}
