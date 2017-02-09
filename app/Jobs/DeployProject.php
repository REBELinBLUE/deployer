<?php

namespace REBELinBLUE\Deployer\Jobs;

use Exception;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Queue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Command as Stage;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Events\DeploymentFinished;
use REBELinBLUE\Deployer\Jobs\DeployProject\CleanupFailedDeployment;
use REBELinBLUE\Deployer\Jobs\DeployProject\LogFormatter;
use REBELinBLUE\Deployer\Jobs\DeployProject\ReleaseArchiver;
use REBELinBLUE\Deployer\Jobs\DeployProject\SendFileToServer;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Parser as ScriptParser;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\User;
use RuntimeException;

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
    private $private_key;

    /**
     * @var string
     */
    private $cache_key;

    /**
     * @var string
     */
    private $release_archive;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var LogFormatter
     */
    private $formatter;

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
     * @param Cache      $cache
     */
    public function handle(Filesystem $filesystem, Cache $cache, LogFormatter $formatter)
    {
        $this->cache      = $cache;
        $this->filesystem = $filesystem;
        $this->formatter  = $formatter;

        $this->runDeployment();
    }

//    /**
//     * The job failed to process.
//     *
//     * @param Exception $exception
//     */
//    public function failed(Exception $exception)
//    {
//        // FIXME: Clean up
//    }

    /**
     * Runs the deployment.
     */
    private function runDeployment()
    {
        $this->deployment->started_at = $this->deployment->freshTimestamp();
        $this->deployment->status     = Deployment::DEPLOYING;
        $this->deployment->save();

        $this->deployment->project->status = Project::DEPLOYING;
        $this->deployment->project->save();

        $this->private_key = $this->filesystem->tempnam(storage_path('app/tmp/'), 'key');
        $this->filesystem->put($this->private_key, $this->deployment->project->private_key);
        $this->filesystem->chmod($this->private_key, 0600);

        $this->release_archive = $this->deployment->project_id . '_' . $this->deployment->release_id . '.tar.gz';

        try {
            $this->dispatch(new UpdateGitMirror($this->deployment->project));

            // If the build has been manually triggered get the committer info from the repo
            $this->updateRepoInfo();

            // FIXME: Should there be jobs or just normal cases?
            $this->dispatch(new ReleaseArchiver($this->deployment, $this->release_archive));

            /** @var Collection $steps */
            $steps = $this->deployment->steps;
            $steps->each(function (DeployStep $step) {
                $this->runStep($step);
            });

            $this->deployment->status          = Deployment::COMPLETED;
            $this->deployment->project->status = Project::FINISHED;
        } catch (Exception $error) {
            $this->deployment->status          = Deployment::FAILED;
            $this->deployment->project->status = Project::FAILED;

            if ($error->getMessage() === 'Cancelled') {
                $this->deployment->status = Deployment::ABORTED;
            }

            $this->cancelPendingSteps();

            if (isset($step)) {
                // Cleanup the release if it has not been activated
                if ($step->stage <= Stage::DO_ACTIVATE) {
                    $this->dispatch(new CleanupFailedDeployment(
                        $this->deployment,
                        $this->release_archive,
                        $this->private_key
                    ));
                } else {
                    $this->deployment->status          = Deployment::COMPLETED_WITH_ERRORS;
                    $this->deployment->project->status = Project::FINISHED;
                }
            }
        }

        if ($this->deployment->status !== Deployment::ABORTED) {
            $this->deployment->finished_at = $this->deployment->freshTimestamp();
        }

        $this->deployment->save();

        $this->deployment->project->last_run = $this->deployment->finished_at;
        $this->deployment->project->save();

        // Notify user or others the deployment has been finished
        event(new DeploymentFinished($this->deployment));

        $to_delete = [$this->private_key];

        $archive = storage_path('app/' . $this->release_archive);
        if ($this->filesystem->exists($archive)) {
            $to_delete[] = $archive;
        }

        $this->filesystem->delete($to_delete);
    }

    /**
     * Clones the repository locally to get the latest log entry and updates the deployment model.
     */
    private function updateRepoInfo()
    {
        $commit = ($this->deployment->commit === Deployment::LOADING ? null : $this->deployment->commit);

        /** @var Process $process */
        $process = app(Process::class);
        $process->setScript('tools.GetCommitDetails', [
            'deployment'    => $this->deployment->id,
            'mirror_path'   => $this->deployment->project->mirrorPath(),
            'git_reference' => $commit ?: $this->deployment->branch,
        ])->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException('Could not get repository info - ' . $process->getErrorOutput());
        }

        $git_info = $process->getOutput();

        list($commit, $committer, $email) = explode("\x09", $git_info);

        $this->deployment->commit          = $commit;
        $this->deployment->committer       = trim($committer);
        $this->deployment->committer_email = trim($email);

        if (!$this->deployment->user_id && !$this->deployment->source) {
            $user = User::where('email', $this->deployment->committer_email)->first();

            if ($user) {
                $this->deployment->user_id = $user->id;
            }
        }

        $this->deployment->save();
    }

    /**
     * Finds all pending steps and marks them as cancelled.
     */
    private function cancelPendingSteps()
    {
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
    }

    /**
     * Executes the commands for a step.
     *
     * @param  DeployStep       $step
     * @throws RuntimeException
     */
    private function runStep(DeployStep $step)
    {
        foreach ($step->servers as $log) {
            $log->status     = ServerLog::RUNNING;
            $log->started_at = $log->freshTimestamp();
            $log->save();

            $server    = $log->server;
            $failed    = false;
            $cancelled = false;

            try {
                $this->sendFilesForStep($step, $log);

                $process = $this->buildScript($step, $server);

                if (!empty($process)) {
                    $output = '';
                    $process->run(function ($type, $output_line) use (&$output, &$log, $process, $step) {
                        if ($type === \Symfony\Component\Process\Process::ERR) {
                            $output .= $this->logError($output_line);
                        } else {
                            $output .= $this->logSuccess($output_line);
                        }

                        $log->output = $output;
                        $log->save();

                        // If there is a cache key, kill the process but leave the key
                        if ($step->stage <= Stage::DO_ACTIVATE && $this->cache->has($this->cache_key)) {
                            $process->stop(0, SIGINT);

                            $output .= $this->logError('SIGINT');
                        }
                    });

                    if (!$process->isSuccessful()) {
                        $failed = true;
                    }

                    $log->output = $output;
                }
            } catch (Exception $e) {
                $log->output .= $this->logError('[' . $server->ip_address . ']: ' . $e->getMessage());
                $failed = true;
            }

            $log->status = ($failed ? ServerLog::FAILED : ServerLog::COMPLETED);

            // Check if there is a cache key and if so abort
            if ($this->cache->pull($this->cache_key) !== null) {
                // Only allow aborting if the release has not yet been activated
                if ($step->stage <= Stage::DO_ACTIVATE) {
                    $log->status = ServerLog::CANCELLED;

                    $cancelled = true;
                    $failed    = false;
                }
            }

            $log->finished_at = $log->freshTimestamp();
            $log->save();

            // Throw an exception to prevent any more tasks running
            if ($failed) {
                throw new RuntimeException('Failed');
            }

            // This is a messy way to do it
            if ($cancelled) {
                throw new RuntimeException('Cancelled');
            }
        }
    }

    /**
     * Sends the files needed to the server.
     *
     * @param DeployStep $step
     * @param ServerLog  $log
     */
    private function sendFilesForStep(DeployStep $step, ServerLog $log)
    {
        $latest_release_dir = $log->server->clean_path . '/releases/' . $this->deployment->release_id;
        $remote_archive     = $log->server->clean_path . '/' . $this->release_archive;
        $local_archive      = storage_path('app/' . $this->release_archive);

        if ($step->stage === Stage::DO_CLONE) {
            $this->sendFile($local_archive, $remote_archive, $log);
        } elseif ($step->stage === Stage::DO_INSTALL) {
            /** @var Collection $files */
            $files = $this->deployment->project->configFiles;
            $files->each(function ($file) use ($latest_release_dir, $log) {
                $this->sendFileFromString($latest_release_dir . '/' . $file->path, $file->content, $log);
            });
        }
    }

    /**
     * Generates the actual bash commands to run on the server.
     *
     * @param DeployStep $step
     * @param Server     $server
     *
     * @return Process
     */
    private function buildScript(DeployStep $step, Server $server)
    {
        $tokens = $this->getTokenList($step, $server);

        // Generate the export
        $exports = '';

        /** @var Collection $variables */
        $variables = $this->deployment->project->variables;
        $variables->each(function ($variable) use (&$exports) {
            $key   = $variable->name;
            $value = $variable->value;

            $exports .= "export {$key}={$value}" . PHP_EOL;
        });

        $user = $server->user;
        if ($step->isCustom()) {
            $user = empty($step->command->user) ? $server->user : $step->command->user;
        }

        // Now get the full script
        return $this->getScriptForStep($step, $tokens)
                    ->prependScript($exports)
                    ->setServer($server, $this->private_key, $user);
    }

    /**
     * Generates an error string to log to the DB.
     *
     * @param string $message
     *
     * @return string
     */
    private function logError($message)
    {
        return '<error>' . $message . '</error>';
    }

    /**
     * Generates an general output string to log to the DB.
     *
     * @param string $message
     *
     * @return string
     */
    private function logSuccess($message)
    {
        return '<info>' . $message . '</info>';
    }

    /**
     * Gets the process which is used for the supplied step.
     *
     * @param DeployStep $step
     * @param array      $tokens
     *
     * @return Process
     */
    private function getScriptForStep(DeployStep $step, array $tokens = [])
    {
        /** @var Process $process */
        $process = app(Process::class);

        switch ($step->stage) {
            case Stage::DO_CLONE:
                return $process->setScript('deploy.steps.CreateNewRelease', $tokens);
            case Stage::DO_INSTALL:
                // Write configuration file to release dir, symlink shared files and run composer
                $process->setScript('deploy.steps.InstallComposerDependencies', $tokens)
                        ->prependScript($this->configurationFileCommands($tokens['release_path']))
                        ->appendScript($this->shareFileCommands($tokens['release_path'], $tokens['shared_path']));

                return $process;
            case Stage::DO_ACTIVATE:
                return $process->setScript('deploy.steps.ActivateNewRelease', $tokens);
            case Stage::DO_PURGE:
                return $process->setScript('deploy.steps.PurgeOldReleases', $tokens);
        }

        // Custom step
        $script = '### Custom script - {{ deployment }}' . PHP_EOL . $step->command->script;

        return $process->setScript($script, $tokens, Process::DIRECT_INPUT);
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
     * Send a string to server.
     *
     * @param string    $remote_path
     * @param string    $content
     * @param ServerLog $log
     */
    private function sendFileFromString($remote_path, $content, ServerLog $log)
    {
        $file = $this->filesystem->tempnam(storage_path('app/tmp/'), 'tmp');
        $this->filesystem->put($file, $content);

        // Upload the file
        $this->sendFile($file, $remote_path, $log);

        $this->filesystem->delete($file);
    }

    /**
     * create the command for sending uploaded files.
     *
     * @param string $release_dir
     *
     * @return string
     */
    private function configurationFileCommands($release_dir)
    {
        /** @var Collection $files */
        $files = $this->deployment->project->configFiles;
        if (!$files->count()) {
            return '';
        }

        $parser = app(ScriptParser::class);

        $script = '';

        foreach ($files as $file) {
            $script .= $parser->parseFile('deploy.ConfigurationFile', [
                'deployment' => $this->deployment->id,
                'path'       => $release_dir . '/' . $file->path,
            ]);
        }

        return $script . PHP_EOL;
    }

    /**
     * Create the script for shared files.
     *
     * @param string $release_dir
     * @param string $shared_dir
     *
     * @return string
     */
    private function shareFileCommands($release_dir, $shared_dir)
    {
        /** @var Collection $files */
        $files = $this->deployment->project->sharedFiles;

        if (!$files->count()) {
            return '';
        }

        $parser = app(ScriptParser::class);

        $script = '';

        foreach ($files as $filecfg) {
            $pathinfo = pathinfo($filecfg->file);
            $template = 'File';

            $file = $filecfg->file;

            if (substr($file, 0, 1) === '/') {
                $file = substr($file, 1);
            }

            if (substr($file, -1) === '/') {
                $template      = 'Directory';
                $file          = substr($file, 0, -1);
            }

            if (isset($pathinfo['extension'])) {
                $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
            } else {
                $filename = $pathinfo['filename'];
            }

            $script .= $parser->parseFile('deploy.Share' . $template, [
                'deployment'  => $this->deployment->id,
                'target_file' => $release_dir . '/' . $file,
                'source_file' => $shared_dir . '/' . $filename,
            ]);
        }

        return PHP_EOL . $script;
    }

    /**
     * Generates the list of tokens for the scripts.
     *
     * @param DeployStep $step
     * @param Server     $server
     *
     * @return array
     */
    private function getTokenList(DeployStep $step, Server $server)
    {
        $releases_dir       = $server->clean_path . '/releases';
        $latest_release_dir = $releases_dir . '/' . $this->deployment->release_id;
        $release_shared_dir = $server->clean_path . '/shared';
        $remote_archive     = $server->clean_path . '/' . $this->release_archive;

        // Set the deployer tags
        $deployer_email = '';
        $deployer_name  = 'webhook';
        if ($this->deployment->user) {
            $deployer_name  = $this->deployment->user->name;
            $deployer_email = $this->deployment->user->email;
        } elseif ($this->deployment->is_webhook && !empty($this->deployment->source)) {
            $deployer_name = $this->deployment->source;
        }

        $tokens = [
            'release'         => $this->deployment->release_id,
            'deployment'      => $this->deployment->id,
            'release_path'    => $latest_release_dir,
            'project_path'    => $server->clean_path,
            'branch'          => $this->deployment->branch,
            'sha'             => $this->deployment->commit,
            'short_sha'       => $this->deployment->short_commit,
            'deployer_email'  => $deployer_email,
            'deployer_name'   => $deployer_name,
            'committer_email' => $this->deployment->committer_email,
            'committer_name'  => $this->deployment->committer,
        ];

        if (!$step->isCustom()) {
            $tokens = array_merge($tokens, [
                'remote_archive' => $remote_archive,
                'include_dev'    => $this->deployment->project->include_dev,
                'builds_to_keep' => $this->deployment->project->builds_to_keep + 1,
                'shared_path'    => $release_shared_dir,
                'releases_path'  => $releases_dir,
            ]);
        }

        return $tokens;
    }
}
