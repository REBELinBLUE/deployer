<?php

namespace REBELinBLUE\Deployer\Events\Observers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Jobs\GenerateKey;
use REBELinBLUE\Deployer\Jobs\RegeneratePublicKey;
use REBELinBLUE\Deployer\Project;

/**
 * Event observer for Project model.
 */
class ProjectObserver
{
    use DispatchesJobs;

    /**
     * Called when the model is being created.
     *
     * @param Project $project
     */
    public function creating(Project $project)
    {
        if (empty($project->private_key)) {
            $this->dispatch(new GenerateKey($project));
        } elseif (empty($project->public_key)) {
            $this->dispatch(new RegeneratePublicKey($project));
        }

        if (empty($project->hash)) {
            $project->generateHash();
        }
    }

    /**
     * Called when the model is being updated.
     *
     * @param Project $project
     */
    public function updating(Project $project)
    {
        if (empty($project->public_key)) {
            $this->dispatch(new RegeneratePublicKey($project));
        }
    }
}
