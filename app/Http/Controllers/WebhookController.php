<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\DeploymentRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;

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

    /**
     * Examines the request to determine which service is calling the webhook.
     *
     * @param  Request $request
     * @param  Project $project
     * @return mixed   Either an array of parameters for the deployment config, or false if it is invalid.
     */
    private function parseWebhookRequest(Request $request, Project $project)
    {
        if ($request->headers->has('X-GitHub-Event')) {
            return $this->githubWebhook($request, $project);
        }

        if ($request->headers->has('X-Gitlab-Event')) {
            return $this->gitlabWebhook($request, $project);
        }

        if ($request->headers->has('X-Event-Key')) {
            return $this->bitbucketWebhook($request, $project);
        }

        return $this->customWebhook($request, $project);
    }

    /**
     * Handles a github webhook request.
     *
     * @param  Request $request
     * @param  Project $project
     * @return mixed   Either an array of parameters for the deployment config, or false if it is invalid.
     */
    private function githubWebhook(Request $request, Project $project)
    {
        // We only care about push events
        if ($request->header('X-GitHub-Event') !== 'push') {
            return false;
        }

        $payload = $request->json();

        // Github sends a payload when you close a pull request with a non-existent commit.
        if ($payload->has('after') && $payload->has('after') === '0000000000000000000000000000000000000000') {
            return false;
        }

        $head = $payload->get('head_commit');

        if ($payload->has('commits')) {
            $branch = str_replace('refs/heads/', '', $payload->get('ref'));

            //commit_id = head['id']
        } else {
            $branch = str_replace('refs/tags/', '', $payload->get('ref'));

            //commit_id = $payload->get('after')
        }

        if (!$project->allow_other_branch && $branch !== $project->branch) {
            return false;
        }

        // Check if the request has an update_only query string and if so check the branch matches
        if ($request->has('update_only') && $request->get('update_only') !== false) {
            $deployment = $this->deploymentRepository->getLatestSuccessful($project->id);

            if (!$deployment || $deployment->branch !== $branch) {
                return false;
            }
        }

        // todo: should we check the following match the repository
        /*
            [repository][git_url] => git://github.com/REBELinBLUE/deployer.git
            [repository][ssh_url] => git@github.com:REBELinBLUE/deployer.git
            [repository][clone_url] => https://github.com/REBELinBLUE/deployer.git
        */

        return [
            'reason'          => $commit['message'],
            'project_id'      => $project->id,
            'branch'          => $branch,
            'optional'        => [],
            'source'          => 'Github',
            'build_url'       => $commit['url'],
            'commit'          => $head['id'],
            'committer'       => $head['committer']['name'],
            'committer_email' => $head['committer']['email'],

        ];

        return false;
    }

    /**
     * Handles a gitlab webhook request.
     *
     * @param  Request $request
     * @param  Project $project
     * @return mixed   Either an array of parameters for the deployment config, or false if it is invalid.
     */
    private function gitlabWebhook(Request $request, Project $project)
    {
        return false;
    }

    /**
     * Handles a bitbucket webhook request.
     *
     * @param  Request $request
     * @param  Project $project
     * @return mixed   Either an array of parameters for the deployment config, or false if it is invalid.
     */
    private function bitbucketWebhook(Request $request, Project $project)
    {
        return false;
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
        $branch = $project->branch;

        // If allow other branches is set, check for post data
        if ($request->has('branch')) {
            $branch = $request->get('branch');

            if (!$project->allow_other_branch && $branch !== $project->branch) {
                return false;
            }
        }

        if ($request->has('update_only') && $request->get('update_only') !== false) {
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

        // TODO: Allow a ref to be parsed in?
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
