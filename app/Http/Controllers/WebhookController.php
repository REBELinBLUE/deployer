<?php

namespace App\Http\Controllers;

use App\Deployment;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Jobs\QueueDeployment;
use App\Project;
use Input;

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
     * Class constructor.
     *
     * @param  ProjectRepositoryInterface    $deploymentRepository
     * @return void
     */
    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * Handles incoming requests from Gitlab or PHPCI to trigger deploy.
     *
     * @param  string   $hash The webhook hash
     * @return Response
     */
    public function webhook($hash)
    {
        // TODO: Check for input, make sure it is a valid gitlab hook, check repo and branch are correct
        // TODO: Allow optional commands to be specified in the POST data

        $project = $this->projectRepository->getByHash($hash);

        $success = false;
        if ($project->servers->where('deploy_code', true)->count() > 0) {
            $optional = [];

            // FIXME: Change to use repostory
            $deployment         = new Deployment;
            $deployment->reason = Input::get('reason');
            $deployment->branch = $project->branch;

            $this->dispatch(new QueueDeployment(
                $project,
                $deployment,
                $optional
            ));

            $success = true;
        }

        return [
            'success' => $success,
        ];
    }

    /**
     * Generates a new webhook URL.
     *
     * @param  Project  $project
     * @return Response
     */
    public function refresh($project_id)
    {
        $project = $this->projectRepository->getById($project_id);
        $project->generateHash();
        $project->save();

        return [
            'url' => route('webhook', $project->hash),
        ];
    }
}
