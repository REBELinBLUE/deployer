<?php

namespace REBELinBLUE\Deployer\Jobs\DeployProject;

use Exception;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Exceptions\CancelledDeploymentException;
use REBELinBLUE\Deployer\Exceptions\FailedDeploymentException;
use REBELinBLUE\Deployer\Jobs\AbortDeployment;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Parser as ScriptParser;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\SharedFile;
use Symfony\Component\Process\Process as SymfonyProcess;

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
     * @var ScriptParser
     */
    private $parser;

    /**
     * @var Process
     */
    private $process;

    /**
     * Create a new job instance.
     *
     * @param Deployment $deployment
     * @param DeployStep $step
     * @param string     $private_key
     * @param $release_archive
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
     * @param Cache        $cache
     * @param LogFormatter $formatter
     * @param Filesystem   $filesystem
     * @param ScriptParser $parser
     * @param Process      $process
     */
    public function handle(
        Cache $cache,
        LogFormatter $formatter,
        Filesystem $filesystem,
        ScriptParser $parser,
        Process $process
    ) {
        $this->cache      = $cache;
        $this->formatter  = $formatter;
        $this->filesystem = $filesystem;
        $this->parser     = $parser;
        $this->process    = $process;

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
            /** @var Server $server */
            $server = $log->server;

            $log->status = ServerLog::RUNNING;
            $log->started_at = $log->freshTimestamp();
            $log->save();

            try {
                $this->sendFilesForStep($log);
                $this->runDeploymentStep($log, $server);

                // Check if there is a cache key and if the release has not yet been activated
                if ($this->cache->pull($this->cache_key) !== null && $this->canBeCancelled()) {
                    throw new CancelledDeploymentException();
                }

                $log->status = ServerLog::COMPLETED;
            } catch (Exception $e) {
                if ($e instanceof CancelledDeploymentException) {
                    $log->status = ServerLog::CANCELLED;

                    throw $e;
                }

                $log->status = ServerLog::FAILED;

                throw new FailedDeploymentException();
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
        $latest_release_dir = $log->server->clean_path . '/releases/' . $this->deployment->release_id;
        $remote_archive     = $log->server->clean_path . '/' . $this->release_archive;
        $local_archive      = storage_path('app/' . $this->release_archive);

        if ($this->step->stage === Command::DO_CLONE) {
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
     * @param Server    $server
     *
     * @throws FailedDeploymentException
     * @throws CancelledDeploymentException
     */
    private function runDeploymentStep(ServerLog $log, Server $server)
    {
        $process = $this->buildScript($server);

        $cancelled = false;

        if (!empty($process)) {
            $output = '';
            $process->run(function ($type, $output_line) use (&$output, &$log, $process, &$cancelled) {
                if ($type === SymfonyProcess::ERR) {
                    $output .= $this->formatter->error($output_line);
                } else {
                    $output .= $this->formatter->info($output_line);
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
     * Generates the actual bash commands to run on the server.
     *
     * @param Server $server
     *
     * @return Process
     */
    private function buildScript(Server $server)
    {
        $tokens = $this->getTokenList($server);

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
        if ($this->step->isCustom()) {
            $user = empty($this->step->command->user) ? $server->user : $this->step->command->user;
        }

        // Now get the full script
        return $this->getScriptForStep($tokens)
                    ->prependScript($exports)
                    ->setServer($server, $this->private_key, $user);
    }

    /**
     * Gets the process which is used for the supplied step.
     *
     * @param array $tokens
     *
     * @return Process
     */
    private function getScriptForStep(array $tokens = [])
    {
        // FIXME: Prepend create directories
        switch ($this->step->stage) {
            case Command::DO_CLONE:
                return $this->process->setScript('deploy.steps.CreateNewRelease', $tokens);
            case Command::DO_INSTALL:
                $release_path = $tokens['release_path'];
                $shared_path  = $tokens['shared_path'];

                // Write configuration file to release dir, symlink shared files and run composer
                return $this->process->setScript('deploy.steps.InstallComposerDependencies', $tokens)
                                     ->prependScript($this->configurationFileCommands($release_path))
                                     ->appendScript($this->shareFileCommands($release_path, $shared_path));
            case Command::DO_ACTIVATE:
                return $this->process->setScript('deploy.steps.ActivateNewRelease', $tokens);
            case Command::DO_PURGE:
                return $this->process->setScript('deploy.steps.PurgeOldReleases', $tokens);
        }

        // Custom step
        $script = '### Custom script - {{ deployment }}' . PHP_EOL . $this->step->command->script;

        return $this->process->setScript($script, $tokens, Process::DIRECT_INPUT);
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

        $script = '';
        $files->each(function (ConfigFile $file) use (&$script, $release_dir) {
            $script .= $this->parser->parseFile('deploy.ConfigurationFile', [
                'deployment' => $this->deployment->id,
                'path'       => $release_dir . '/' . $file->path,
            ]);
        });

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

        $script = '';
        $files->each(function (SharedFile $shared) use (&$script, $release_dir, $shared_dir) {
            $pathinfo = pathinfo($shared->file);
            $template = 'File';

            $file = $shared->file;

            if (starts_with($file, '/')) {
                $file = substr($file, 1);
            }

            if (ends_with($file, '/')) {
                $template = 'Directory';
                $file     = substr($file, 0, -1);
            }

            $filename = $pathinfo['filename'];
            if (isset($pathinfo['extension'])) {
                $filename .= '.' . $pathinfo['extension'];
            }

            $script .= $this->parser->parseFile('deploy.Share' . $template, [
                'deployment'  => $this->deployment->id,
                'target_file' => $release_dir . '/' . $file,
                'source_file' => $shared_dir . '/' . $filename,
            ]);
        });

        return PHP_EOL . $script;
    }

    /**
     * Generates the list of tokens for the scripts.
     *
     * @param Server $server
     *
     * @return array
     */
    private function getTokenList(Server $server)
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

        if (!$this->step->isCustom()) {
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
