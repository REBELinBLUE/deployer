<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Contracts\Repositories\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Http\Webhooks\Beanstalkapp;
use REBELinBLUE\Deployer\Http\Webhooks\Bitbucket;
use REBELinBLUE\Deployer\Http\Webhooks\Custom;
use REBELinBLUE\Deployer\Http\Webhooks\Github;
use REBELinBLUE\Deployer\Http\Webhooks\Gitlab;
use REBELinBLUE\Deployer\Http\Webhooks\Gogs;
use REBELinBLUE\Deployer\Project;

/**
 * The deployment webhook controller.
 */
class WebhookController extends Controller
{
    /**
     * List of supported service classes.
     *
     * @var array
     */
    private $services = [
        Beanstalkapp::class,
        Bitbucket::class,
        Github::class,
        Gitlab::class,
        Gogs::class,
    ];

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
     * WebhookController constructor.
     *
     * @param ProjectRepositoryInterface    $projectRepository
     * @param DeploymentRepositoryInterface $deploymentRepository
     */
    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        DeploymentRepositoryInterface $deploymentRepository
    ) {
        $this->projectRepository    = $projectRepository;
        $this->deploymentRepository = $deploymentRepository;

        $this->services[] = Custom::class;
    }

    /**
     * Handles incoming requests to trigger deploy.
     *
     * @param Request $request
     * @param string  $hash    The webhook hash
     *
     * @return \Illuminate\View\View
     */
    public function webhook(Request $request, $hash)
    {
        $project = $this->projectRepository->getByHash($hash);

        $success = false;
        if ($project->servers->where('deploy_code', true)->count() > 0) {
            $payload = $this->parseWebhookRequest($request, $project);

            if (is_array($payload)) {
                $this->deploymentRepository->abortQueued($project->id);
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
     * Then adds the various additional details required to trigger a deployment.
     *
     * @param Request $request
     * @param Project $project
     *
     * @return array|false Either an array of parameters for the deployment config, or false if it is invalid.
     */
    private function parseWebhookRequest(Request $request, Project $project)
    {
        foreach ($this->services as $service) {
            $integration = new $service($request);

            if ($integration->isRequestOrigin()) {
                return $this->appendProjectSettings($integration->handlePush(), $request, $project);
            }
        }

        return false;
    }

    /**
     * Takes the data returned from the webhook request and then adds deployers own data, such as project ID
     * and runs any checks such as checks the branch is allowed to be deployed.
     *
     * @param array   $payload
     * @param Request $request
     * @param Project $project
     *
     * @return array|false Either an array of the complete deployment config, or false if it is invalid.
     */
    private function appendProjectSettings($payload, Request $request, Project $project)
    {
        // If the payload is empty return false
        if (!is_array($payload) || !count($payload)) {
            return false;
        }

        $payload['project_id'] = $project->id;

        // If there is no branch set get it from the project
        if (is_null($payload['branch']) || empty($payload['branch'])) {
            $payload['branch'] = $project->branch;
        }

        // If the project doesn't allow other branches check the requested branch is the correct one
        if (!$project->allow_other_branch && $payload['branch'] !== $project->branch) {
            return false;
        }

        $payload['optional'] = [];

        // Check if the commands input is set, if so explode on comma and filter out any invalid commands
        if ($request->has('commands')) {
            $valid     = $project->commands->pluck('id');
            $requested = explode(',', $request->get('commands'));

            $payload['optional'] = collect($requested)->unique()
                                                      ->intersect($valid)
                                                      ->toArray();
        }

        // If the webhook is allowed to deploy other branches check if the request has an update_only
        // query string and if so check the branch matches that which is currently deployed
        if ($project->allow_other_branch && $request->has('update_only') && $request->get('update_only') !== false) {
            $deployment = $this->deploymentRepository->getLatestSuccessful($project->id);

            if (!$deployment || $deployment->branch !== $payload['branch']) {
                return false;
            }
        }

        return $payload;
    }

    /**
     * Generates a new webhook URL.
     *
     * @param int $project_id
     *
     * @return \Illuminate\View\View
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
