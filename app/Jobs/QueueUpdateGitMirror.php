<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Project;

/**
 * Extends the UpdateGitMirror job so that is it queued.
 */
class QueueUpdateGitMirror extends Job implements ShouldQueue
{
    use SerializesModels, DispatchesJobs;

    /**
     * @var int
     */
    public $timeout = 0;

    /**
     * @var Project
     */
    private $project;

    /**
     * Constructor.
     *
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Handles the job.
     */
    public function handle()
    {
        $this->dispatch(new UpdateGitMirror($this->project));
    }
}
