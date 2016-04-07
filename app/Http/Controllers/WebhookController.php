<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Contracts\Repositories\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Contracts\Repositories\ProjectRepositoryInterface;

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
            $data = $this->parseWebhookRequest($request, $project);

            if (is_array($data)) {
                $this->deploymentRepository->create($data);

                $success = true;
            }
        }

        return [
            'success' => $success,
        ];
    }

    private function parseWebhookRequest(Request $request, $project)
    {
        if ($request->headers->has('X-GitHub-Delivery')) {
            return $this->githubWebhook($request, $project);
        }

        return $this->customWebhook($request, $project);
    }

    private function githubWebhook(Request $request, $project)
    {
    }

    private function gitlabWebhook(Request $request, $project)
    {
    }

    private function bitbucketWebhook(Request $request, $project)
    {
    }

    private function customWebhook(Request $request, $project)
    {
        // Get the branch if it is the request, otherwise deploy the default branch
        $branch = $project->branch;

        // If allow other branches is set, check for post data
        if ($request->has('branch')) {
            $branch = $request->get('branch');

            if (!$project->allow_other_branch && $branch !== $project->branch) {
                return false;
            }
        }

        if ($do_deploy && $request->has('update_only') && $request->get('update_only') !== false) {
            // Get the latest deployment and check the branch matches
            $deployment = $this->deploymentRepository->getLatestSuccessful($project->id);

            if (!$deployment || $deployment->branch !== $branch) {
                return false;
            }
        }

        $optional = [];

        // Check if the commands input is set, if so explode on comma and filter out any invalid commands
        if ($request->has('commands')) {
            $valid = $project->commands->lists('id');

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
