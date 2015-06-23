<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\DeploymentRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
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
     * Handles incoming requests from Gitlab or PHPCI to trigger deploy.
     *
     * @param  string   $hash The webhook hash
     * @return Response
     */
    public function webhook($hash)
    {
        $project = $this->projectRepository->getByHash($hash);

        $success = false;
        if ($project->servers->where('deploy_code', true)->count() > 0) {
            $data = [
                'reason'     => Input::get('reason'),
                'project_id' => $project->id,
                'branch'     => $project->branch,
                'optional'   => [],
            ];

            $this->deploymentRepository->create($data);

            $success = true;
        }

        return [
            'success' => $success,
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
            'url' => route('webhook', $project->hash),
        ];
    }
}
