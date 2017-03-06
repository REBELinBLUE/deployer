<?php

namespace REBELinBLUE\Deployer\Jobs\DeployProject;

use Illuminate\Cache\Repository as Cache;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Exceptions\CancelledDeploymentException;
use REBELinBLUE\Deployer\Exceptions\FailedDeploymentException;
use REBELinBLUE\Deployer\Jobs\AbortDeployment;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Runs a step of the deployment.
 */
class RunDeploymentStep
{
    use SerializesModels, DispatchesJobs;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var LogFormatter
     */
    private $formatter;

    /**
     * @var DeployStep
     */
    private $step;

    /**
     * @var string
     */
    private $private_key;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var string
     */
    private $release_archive;

    /**
     * @var string
     */
    private $cache_key;

    /**
     * @var ScriptBuilder
     */
    private $builder;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Create a new job instance.
     *
     * @param Deployment $deployment
     * @param DeployStep $step
     * @param string     $private_key
     * @param string     $release_archive
     */
    public function __construct(Deployment $deployment, DeployStep $step, $private_key, $release_archive)
    {
        $this->cache_key       = AbortDeployment::CACHE_KEY_PREFIX . $deployment->id;
        $this->deployment      = $deployment;
        $this->step            = $step;
        $this->private_key     = $private_key;
        $this->release_archive = $release_archive;
    }

    /**
     * Execute the job.
     *
     * @param Cache         $cache
     * @param LogFormatter  $formatter
     * @param Filesystem    $filesystem
     * @param ScriptBuilder $builder
     */
    public function handle(
        Cache $cache,
        LogFormatter $formatter,
        Filesystem $filesystem,
        ScriptBuilder $builder
    ) {
        $this->cache      = $cache;
        $this->formatter  = $formatter;
        $this->filesystem = $filesystem;
        $this->builder    = $builder;

        $builder->setup($this->deployment, $this->step, $this->release_archive, $this->private_key);

        $this->run();
    }

    /**
     * Runs the actual deployment step.
     */
    private function run()
    {
        /** @var Collection $servers */
        $servers = $this->step->servers;
        $servers->each(function (ServerLog $log) {
            $log->status = ServerLog::RUNNING;
            $log->started_at = $log->freshTimestamp();
            $log->save();

            try {
                $this->sendFilesForStep($log);
                $this->runDeploymentStepOnServer($log);

                // Check if there is a cache key and if the release has not yet been activated
                if ($this->cache->pull($this->cache_key) !== null && $this->canBeCancelled()) {
                    // FIXME: This is horrible, using exception as flow control
                    throw new CancelledDeploymentException();
                }

                $log->status = ServerLog::COMPLETED;
            } catch (CancelledDeploymentException $e) {
                $log->status = ServerLog::CANCELLED;
                throw $e;
            } catch (FailedDeploymentException $e) {
                $log->status = ServerLog::FAILED;
                throw $e;
            } finally {
                $log->finished_at = $log->freshTimestamp();
                $log->save();
            }
        });
    }

    /**
     * Sends the files needed to the server.
     *
     * @param ServerLog $log
     */
    private function sendFilesForStep(ServerLog $log)
    {
        if ($this->step->stage !== Command::DO_CLONE && $this->step->stage !== Command::DO_INSTALL) {
            return;
        }

        $latest_release_dir = $log->server->clean_path . '/releases/' . $this->deployment->release_id;

        if ($this->step->stage === Command::DO_CLONE) {
            $remote_archive = $log->server->clean_path . '/' . $this->release_archive;
            $local_archive  = storage_path('app/' . $this->release_archive);

            $this->sendFile($local_archive, $remote_archive, $log);
        } elseif ($this->step->stage === Command::DO_INSTALL) {
            /** @var Collection $files */
            $files = $this->deployment->project->configFiles;
            $files->each(function ($file) use ($latest_release_dir, $log) {
                $this->sendFileFromString($latest_release_dir . '/' . $file->path, $file->content, $log);
            });
        }
    }

    /**
     * @param ServerLog $log
     *
     * @throws FailedDeploymentException
     * @throws CancelledDeploymentException
     */
    private function runDeploymentStepOnServer(ServerLog $log)
    {
        $process = $this->builder->buildScript($log->server);

        if (!empty($process)) {
            $cancelled = false;
            $output    = '';
            $process->run(function ($type, $line) use (&$output, &$log, $process, &$cancelled) {
                if ($type === Process::ERR) {
                    $output .= $this->formatter->error($line);
                } else {
                    $output .= $this->formatter->info($line);
                }

                $log->output = $output;
                $log->save();

                // If there is a cache key, kill the process but leave the key
                if ($this->cache->has($this->cache_key) && $this->canBeCancelled()) {
                    $process->stop(0, SIGINT);

                    $output .= $this->formatter->error('SIGINT - Cancelled');

                    $cancelled = true;
                }
            });

            $log->output = $output;

            if (!$process->isSuccessful()) {
                if ($cancelled) {
                    throw new CancelledDeploymentException();
                }

                throw new FailedDeploymentException($process->getErrorOutput());
            }
        }
    }

    /**
     * Send a string to server.
     *
     * @param string    $remote_path
     * @param string    $content
     * @param ServerLog $log
     */
    private function sendFileFromString($remote_path, $content, ServerLog $log)
    {
        $local_file = $this->filesystem->tempnam(storage_path('app/tmp/'), 'tmp');
        $this->filesystem->put($local_file, $content);

        // Upload the file
        $this->sendFile($local_file, $remote_path, $log);

        $this->filesystem->delete($local_file);
    }

    /**
     * Sends a file to a remote server.
     *
     * @param string    $local_file
     * @param string    $remote_file
     * @param ServerLog $log
     *
     * @throws RuntimeException
     */
    private function sendFile($local_file, $remote_file, ServerLog $log)
    {
        $this->dispatch(new SendFileToServer(
            $this->deployment,
            $log,
            $local_file,
            $remote_file,
            $this->private_key
        ));
    }

    /**
     * Whether or not the step can be cancelled.
     *
     * @return bool
     */
    private function canBeCancelled()
    {
        return $this->step->stage <= Command::DO_ACTIVATE;
    }
}
