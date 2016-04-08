<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELiuBLUE\Deployer\Webhooks\Beanstalkapp;
use REBELiuBLUE\Deployer\Webhooks\Bitbucket;
use REBELiuBLUE\Deployer\Webhooks\Github;
use REBELiuBLUE\Deployer\Webhooks\Gitlab;

/**
 * The deployment webhook controller.
 */
class WebhookController extends Controller
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
     * @param  DeploymentRepositoryInterface $deploymentRepository
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
     * Handles incoming requests to trigger deploy.
     *
     * @param  Request  $request
     * @param  string   $hash    The webhook hash
     * @return Response
     */
    public function webhook(Request $request, $hash)
    {
        $project = $this->projectRepository->getByHash($hash);

        $success = false;
        if ($project->servers->where('deploy_code', true)->count() > 0) {
            $payload = $this->parseWebhookRequest($request, $project);

            /*
            TODO: Reimplement this
            if (!$project->allow_other_branch && $data['branch'] !== $project->branch) {
                return false;
            }

            // Check if the request has an update_only query string and if so check the branch matches
            if ($request->has('update_only') && $request->get('update_only') !== false) {
                $deployment = $this->deploymentRepository->getLatestSuccessful($project->id);

                if (!$deployment || $deployment->branch !== $data['branch']) {
                    return false;
                }
            }
             */

            if (is_array($payload)) {
                $this->deploymentRepository->create($payload);

                $success = true;
            }
        }

        return [
            'success' => $success,
        ];
    }

    /**
     * Goes through the various webhook integrations as checks if the request is for them and parses it.
     *
     * @param  Request $request
     * @param  Project $project
     * @return mixed   Either an array of parameters for the deployment config, or false if it is invalid.
     */
    private function parseWebhookRequest(Request $request, Project $project)
    {
        foreach (['Github', 'Gitlab', 'Bitbucket', 'Beanstalkapp'] as $service) {
            $integration = new $service($request);

            if ($integration->isRequestOrigin()) {
                return $integration->handlePush();
            }
        }

        return $this->customWebhook($request, $project);
    }

    /**
     * Handles Deployer's custom webhook request.
     *
     * @param  Request $request
     * @param  Project $project
     * @return mixed   Either an array of parameters for the deployment config, or false if it is invalid.
     */
    private function customWebhook(Request $request, $project)
    {
        // Get the branch if it is the request, otherwise deploy the default branch
        $branch = $request->has('branch') ? $request->get('branch') : $project->branch;

        $optional = [];

        // Check if the commands input is set, if so explode on comma and filter out any invalid commands
        if ($request->has('commands')) {
            $valid = $project->commands->lists('id'); // fixme this should be done in the same place as the block on line 64

            $optional = collect(explode(',', $request->get('commands')))
                                ->unique()
                                ->intersect($valid);
        }

        // If there is a source and a URL validate that the URL is valid
        $build_url = null;
        if ($request->has('source') && $request->has('url')) {
            $build_url = $request->get('url');

            if (!filter_var($build_url, FILTER_VALIDATE_URL)) {
                $build_url = null;
            }
        }

        // TODO: Allow a ref to be passed in?
        return [
            'reason'     => $request->get('reason'),
            'project_id' => $project->id,
            'branch'     => $branch,
            'optional'   => $optional,
            'source'     => $request->get('source'),
            'build_url'  => $build_url,
        ];
    }

    /**
     * Generates a new webhook URL.
     *
     * @param  int      $project_id
     * @return Response
     */
    public function refresh($project_id)
    {
        $project = $this->projectRepository->getById($project_id);
        $project->generateHash();
        $project->save();

        return [
            'url' => route('webhook.deploy', $project->hash),
        ];
    }
}
