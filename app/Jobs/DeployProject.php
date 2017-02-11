<?php

namespace REBELinBLUE\Deployer\Jobs;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Queue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Events\DeploymentFinished;
use REBELinBLUE\Deployer\Exceptions\CancelDeploymentException;
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
     * @var Deployment
     */
    private $deployment;

    /**
     * @var string
     */
    private $cache_key;

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
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * DeployProject constructor.
     *
     * @param Deployment $deployment
     */
    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
        $this->cache_key  = AbortDeployment::CACHE_KEY_PREFIX . $deployment->id;
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
     * @param Dispatcher $dispatcher
     */
    public function handle(Filesystem $filesystem, Dispatcher $dispatcher)
    {
        $this->filesystem = $filesystem;
        $this->dispatcher = $dispatcher;

        $this->deployment->started_at = $this->deployment->freshTimestamp();
        $this->deployment->status     = Deployment::DEPLOYING;
        $this->deployment->save();

        $this->deployment->project->status = Project::DEPLOYING;
        $this->deployment->project->save();

        $this->archive = $this->deployment->project_id . '_' . $this->deployment->release_id . '.tar.gz';

        $this->private_key = $this->filesystem->tempnam(storage_path('app/tmp/'), 'key');
        $this->filesystem->put($this->private_key, $this->deployment->project->private_key);
        $this->filesystem->chmod($this->private_key, 0600);

        $this->dispatch(new UpdateGitMirror($this->deployment->project));
        $this->dispatch(new UpdateRepositoryInfo($this->deployment));
        $this->dispatch(new ReleaseArchiver($this->deployment, $this->archive));

        /** @var Collection $steps */
        $steps = $this->deployment->steps;
        $steps->each(function (DeployStep $step) {
            $this->dispatch(new RunDeploymentStep($step, $this->private_key));
        });

        $this->deployment->status          = Deployment::COMPLETED;
        $this->deployment->project->status = Project::FINISHED;
        $this->deployment->finished_at     = $this->deployment->freshTimestamp();

        $this->finish();
    }

    /**
     * The job failed to process.
     *
     * @param Exception $error
     */
    public function failed(Exception $error)
    {
        $this->deployment->status          = Deployment::FAILED;
        $this->deployment->project->status = Project::FAILED;

        if ($error instanceof CancelDeploymentException) {
            $this->deployment->status = Deployment::ABORTED;
        }

        /** @var Collection $steps */
        $steps = $this->deployment->steps;
        $steps->each(function (DeployStep $step) {
            /** @var Collection $servers */
            $servers = $step->servers;
            $servers->filter(function (ServerLog $log) {
                return $log->status === ServerLog::PENDING;
            })->each(function (ServerLog $log) {
                $log->status = ServerLog::CANCELLED;
                $log->save();
            });
        });

        if (isset($step)) { // FIXME: This no longer works
            // Cleanup the release if it has not been activated
            if ($step->stage <= Stage::DO_ACTIVATE) {
                $this->dispatch(new CleanupFailedDeployment(
                    $this->deployment,
                    $this->archive,
                    $this->private_key
                ));
            } else {
                $this->deployment->status          = Deployment::COMPLETED_WITH_ERRORS;
                $this->deployment->project->status = Project::FINISHED;
            }
        }

        $this->finish();
    }

    /**
     * Cleans up when the deployment has finished.
     * @fixme: should this just be the __destruct method?
     */
    private function finish()
    {
        $this->deployment->save();

        $this->deployment->project->last_run = $this->deployment->finished_at;
        $this->deployment->project->save();

        // Notify user or others the deployment has been finished
        $this->dispatcher->dispatch(new DeploymentFinished($this->deployment));

        $to_delete = [$this->private_key];

        $archive = storage_path('app/' . $this->archive);
        if ($this->filesystem->exists($archive)) {
            $to_delete[] = $archive;
        }

        $this->filesystem->delete($to_delete);
    }
}
