<?php

namespace REBELinBLUE\Deployer\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Queue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Events\DeploymentFinished;
use REBELinBLUE\Deployer\Exceptions\CancelledDeploymentException;
use REBELinBLUE\Deployer\Exceptions\DeploymentException;
use REBELinBLUE\Deployer\Jobs\DeployProject\CleanupFailedDeployment;
use REBELinBLUE\Deployer\Jobs\DeployProject\ReleaseArchiver;
use REBELinBLUE\Deployer\Jobs\DeployProject\RunDeploymentStep;
use REBELinBLUE\Deployer\Jobs\DeployProject\UpdateRepositoryInfo;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;

/**
 * Deploys an actual project.
 */
class DeployProject extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    /**
     * @var int
     */
    public $timeout = 0;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $private_key;

    /**
     * @var string
     */
    private $archive;

    /**
     * DeployProject constructor.
     *
     * @param Deployment $deployment
     */
    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
        $this->archive    = $this->deployment->project_id . '_' . $this->deployment->release_id . '.tar.gz';
    }

    /**
     * Overwrite the queue method to push to a different queue.
     *
     * @param Queue $queue
     * @param Job   $command
     */
    public function queue(Queue $queue, $command)
    {
        $queue->pushOn('deployer-high', $command);
    }

    /**
     * Execute the command.
     *
     * @param Filesystem $filesystem
     */
    public function handle(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        $this->waitIfMirroring();

        $this->deployment->started_at = $this->deployment->freshTimestamp();
        $this->deployment->status     = Deployment::DEPLOYING;
        $this->deployment->save();

        $this->deployment->project->status = Project::DEPLOYING;
        $this->deployment->project->save();

        $this->private_key = $this->filesystem->tempnam(storage_path('app/tmp/'), 'key');
        $this->filesystem->put($this->private_key, $this->deployment->project->private_key);
        $this->filesystem->chmod($this->private_key, 0600);

        try {
            $this->dispatch(new UpdateGitMirror($this->deployment->project));
            $this->dispatch(new UpdateRepositoryInfo($this->deployment));
            $this->dispatch(new ReleaseArchiver($this->deployment, $this->archive));

            /** @var Collection $steps */
            $steps = $this->deployment->steps;
            $steps->each(function (DeployStep $step) {
                $this->dispatch(new RunDeploymentStep($this->deployment, $step, $this->private_key, $this->archive));
            });

            $this->deployment->status          = Deployment::COMPLETED;
            $this->deployment->project->status = Project::FINISHED;
            $this->deployment->finished_at     = $this->deployment->freshTimestamp();
        } catch (Exception $error) {
            $this->fail($error);
        } finally {
            $this->cleanup();
        }
    }

    /**
     * The job failed to process.
     * We can't use laravel's built in "failed" method as filesystem and events properties are then unset.
     *
     * @param Exception $error
     */
    private function fail(Exception $error)
    {
        $this->deployment->project->status = Project::FAILED;
        $this->deployment->status          = Deployment::FAILED;

        if ($error instanceof CancelledDeploymentException) {
            $this->deployment->status = Deployment::ABORTED;
        } else {
            $this->deployment->finished_at = $this->deployment->freshTimestamp();
        }

        $activated = true;

        /** @var Collection $steps */
        $steps = $this->deployment->steps;
        $steps->each(function (DeployStep $step) use (&$activated) {
            /** @var Collection $servers */
            $servers = $step->servers;
            $servers->filter(function (ServerLog $log) {
                return $log->status === ServerLog::PENDING;
            })->each(function (ServerLog $log) use ($step, &$activated) {
                if ($step->stage <= Command::DO_ACTIVATE) {
                    $activated = false;
                }

                $log->status = ServerLog::CANCELLED;
                $log->save();
            });
        });

        if ($error instanceof DeploymentException) {
            if (!$activated) {
                $this->dispatch(new CleanupFailedDeployment($this->deployment, $this->archive, $this->private_key));
            } else {
                $this->deployment->status          = Deployment::COMPLETED_WITH_ERRORS;
                $this->deployment->project->status = Project::FINISHED;
            }
        }
    }

    /**
     * Cleans up when the deployment has finished.
     */
    private function cleanup()
    {
        $this->deployment->save();

        $this->deployment->project->last_run = $this->deployment->finished_at;
        $this->deployment->project->save();

        // Notify user or others the deployment has been finished
        event(new DeploymentFinished($this->deployment));

        $to_delete = [$this->private_key];

        $archive = storage_path('app/' . $this->archive);
        if ($this->filesystem->exists($archive)) {
            $to_delete[] = $archive;
        }

        $this->filesystem->delete($to_delete);
    }

    /**
     * Waits whilst the repo is being updated.
     */
    private function waitIfMirroring()
    {
        $isMirroring = $this->deployment->project->is_mirroring;
        while ($isMirroring) {
            sleep(5);

            /** @var Project $project */
            $project     = $this->deployment->project->fresh();
            $isMirroring = $project->is_mirroring;
        }
    }
}
