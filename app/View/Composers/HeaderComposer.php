<?php

namespace REBELinBLUE\Deployer\View\Composers;

use Illuminate\Contracts\View\View;
use REBELinBLUE\Deployer\Contracts\Repositories\DeploymentRepositoryInterface;

/**
 * View composer for the header bar.
 */
class HeaderComposer
{
    /**
     * @var DeploymentRepositoryInterface
     */
    private $deploymentRepository;

    /**
     * HeaderComposer constructor.
     *
     * @param DeploymentRepositoryInterface $deploymentRepository
     */
    public function __construct(DeploymentRepositoryInterface $deploymentRepository)
    {
        $this->deploymentRepository = $deploymentRepository;
    }

    /**
     * Generates the pending and deploying projects for the view.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $pending = $this->deploymentRepository->getPending();

        $view->with('pending', $pending);
        $view->with('pending_count', count($pending));

        $deploying = $this->deploymentRepository->getRunning();

        $view->with('deploying', $deploying);
        $view->with('deploying_count', count($deploying));
    }
}
