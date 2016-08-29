<?php

namespace REBELinBLUE\Deployer\Composers;

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
        $view->with('pending', $this->deploymentRepository->getPending());
        $view->with('deploying', $this->deploymentRepository->getRunning());
    }
}
