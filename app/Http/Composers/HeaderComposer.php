<?php namespace App\Http\Composers;

use Illuminate\Contracts\View\View;
use App\Repositories\Contracts\DeploymentRepositoryInterface;

/**
 * View composer for the header bar
 */
class HeaderComposer
{
    private $deploymentRepository;

    /**
     * Class constructor
     *
     * @param DeploymentRepositoryInterface $user
     */
    public function __construct(DeploymentRepositoryInterface $deploymentRepository)
    {
        $this->deploymentRepository = $deploymentRepository;
    }

    /**
     * Generates the pending and deploying projects for the view
     *
     * @param \Illuminate\Contracts\View\View $view
     * @return void
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
