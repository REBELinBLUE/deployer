<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Queue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use REBELinBLUE\Deployer\Command as Stage;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Events\DeployFinished;
use REBELinBLUE\Deployer\Jobs\Job;
use REBELinBLUE\Deployer\Jobs\UpdateGitMirror;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Scripts\Parser as ScriptParser;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\User;
use Symfony\Component\Process\Process;

/**
 * Deploys an actual project.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * TODO: rewrite this as it is doing way too much and is very messy now.
 * TODO: Expand all parameters
 */
class DeployProject extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    private $deployment;
    private $private_key;
    private $cache_key;
    private $release_archive;
    private $release_id;

    /**
     * Create a new command instance.
     *
     * @param  Deployment    $deployment
     * @return DeployProject
     */
    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
        $this->cache_key  = AbortDeployment::CACHE_KEY_PREFIX . $deployment->id;
    }

    /**
     * Overwrite the queue method to push to a different queue.
     *
     * @param  Queue         $queue
     * @param  DeployProject $command
     * @return void
     */
    public function queue(Queue $queue, $command)
    {
        $queue->pushOn('deployer-high', $command);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $project = $this->deployment->project;

        $this->deployment->started_at = date('Y-m-d H:i:s');
        $this->deployment->status     = Deployment::DEPLOYING;
        $this->deployment->save();

        $project->status = Project::DEPLOYING;
        $project->save();

        $this->release_id = date('YmdHis', strtotime($this->deployment->started_at));

        $this->private_key = tempnam(storage_path('app/'), 'sshkey');
        file_put_contents($this->private_key, $project->private_key);

        $this->release_archive = sprintf(
            '%s/%d_%s.tar.gz',
            storage_path('app'),
            $this->deployment->project_id,
            $this->release_id
        );

        $this->dispatch(new UpdateGitMirror($this->deployment->project));

        try {
            // If the build has been manually triggered get the committer info from the repo
            if ($this->deployment->commit === Deployment::LOADING) {
                $this->updateRepoInfo();
            }

            $this->createReleaseArchive();

            foreach ($this->deployment->steps as $step) {
                $this->runStep($step);
            }

            $this->deployment->status = Deployment::COMPLETED;
            $project->status          = Project::FINISHED;
        } catch (\Exception $error) {
            $this->deployment->status = Deployment::FAILED;
            $project->status          = Project::FAILED;

            if ($error->getMessage() === 'Cancelled') {
                $this->deployment->status = Deployment::ABORTED;
            }

            $this->cancelPendingSteps($this->deployment->steps);

            if (isset($step)) {
                // Cleanup the release if it has not been activated
                if ($step->stage <= Stage::DO_ACTIVATE) {
                    $this->cleanupDeployment();
                } else {
                    $this->deployment->status = Deployment::COMPLETED_WITH_ERRORS;
                    $project->status          = Project::FINISHED;
                }
            }
        }

        $this->deployment->finished_at = date('Y-m-d H:i:s');
        $this->deployment->save();

        $project->last_run = date('Y-m-d H:i:s');
        $project->save();

        // Notify user or others the deployment has been finished
        event(new DeployFinished($project, $this->deployment));

        unlink($this->private_key);

        if (file_exists($this->release_archive)) {
            unlink($this->release_archive);
        }
    }

    /**
     * Clones the repository locally to get the latest log entry and updates
     * the deployment model.
     *
     * @return void
     */
    private function updateRepoInfo()
    {
        $cmd = with(new ScriptParser)->parseFile('tools.GetCommitDetails', [
            'mirror_path'   => $this->deployment->project->mirrorPath(),
            'git_reference' => $this->deployment->branch,
        ]);

        Log::info($cmd);

        $process = new Process($cmd);
        $process->setTimeout(null);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Could not get repository info - ' . $process->getErrorOutput());
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
     * Creates the archive for the commit to deploy.
     *
     * @return void
     */
    private function createReleaseArchive()
    {
        $cmd = with(new ScriptParser)->parseFile('deploy.CreateReleaseArchive', [
            'mirror_path'     => $this->deployment->project->mirrorPath(),
            'sha'             => $this->deployment->commit,
            'release_archive' => $this->release_archive,
        ]);

        $process = new Process($cmd);
        $process->setTimeout(null);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Could not get repository info - ' . $process->getErrorOutput());
        }
    }

    /**
     * Remove left over artifacts from a failed deploy on each server.
     *
     * @return void
     */
    private function cleanupDeployment()
    {
        $project = $this->deployment->project;

        foreach ($project->servers as $server) {
            if (!$server->deploy_code) {
                continue;
            }

            $root_dir = preg_replace('#/$#', '', $server->path); // FIXME make server saving remove the trailing slash

            if (empty($root_dir)) {
                continue;
            }

            $script = with(new ScriptParser)->parseFile('deploy.CleanupFailedRelease', [
                'project_path'   => $root_dir,
                'release_path'   => $root_dir . '/releases/' . $this->release_id,
                'remote_archive' => $root_dir . '/' . $project->id . '_' . $this->release_id . '.tar.gz',
            ]);

            $process = new Process($this->generateSSHCommand($server, $script));
            $process->setTimeout(null);
            $process->run();
        }
    }

    /**
     * Finds all pending steps and marks them as cancelled.
     *
     * @return void
     */
    private function cancelPendingSteps()
    {
        foreach ($this->deployment->steps as $step) {
            foreach ($step->servers as $log) {
                if ($log->status === ServerLog::PENDING) {
                    $log->status = ServerLog::CANCELLED;
                    $log->save();
                }
            }
        }
    }

    /**
     * Executes the commands for a step.
     *
     * @param  DeployStep        $step
     * @throws \RuntimeException
     * @return void
     */
    private function runStep(DeployStep $step)
    {
        foreach ($step->servers as $log) {
            $log->status     = ServerLog::RUNNING;
            $log->started_at = date('Y-m-d H:i:s');
            $log->save();

            try {
                $server = $log->server;

                $this->sendFilesForStep($step, $server, $log);

                // FIME: Have a getFiles method here for transferring files
                $script = $this->buildScript($step, $server, $log);

                $user = $server->user;
                if (isset($step->command)) {
                    $user = $step->command->user;
                }

                $failed    = false;
                $cancelled = false;

                if (!empty($script)) {
                    $process = new Process($this->generateSSHCommand($server, $script, $user));
                    $process->setTimeout(null);

                    $output = '';
                    $process->run(function ($type, $output_line) use (&$output, &$log, $process, $step) {
                        if ($type === Process::ERR) {
                            $output .= $this->logError($output_line);
                        } else {
                            $output .= $this->logSuccess($output_line);
                        }

                        $log->output = $output;
                        $log->save();

                        // If there is a cache key, kill the process but leave the key
                        if ($step->stage <= Stage::DO_ACTIVATE && Cache::has($this->cache_key)) {
                            $process->stop(0, SIGINT);

                            $output .= $this->logError('SIGINT');
                        }
                    });

                    if (!$process->isSuccessful()) {
                        $failed = true;
                    }

                    $log->output = $output;
                }
            } catch (\Exception $e) {
                // FIXME: In debug mode log this?
                $log->output .= $this->logError('[' . $server->ip_address . ']: ' . $e->getMessage());
                $failed = true;
            }

            $log->status = ($failed ? ServerLog::FAILED : ServerLog::COMPLETED);

            // Check if there is a cache key and if so abort
            if (Cache::pull($this->cache_key) !== null) {

                // Only allow aborting if the release has not yet been activated
                if ($step->stage <= Stage::DO_ACTIVATE) {
                    $log->status = ServerLog::CANCELLED;

                    $cancelled = true;
                    $failed    = false;
                }
            }

            $log->finished_at = date('Y-m-d H:i:s');
            $log->save();

            // Throw an exception to prevent any more tasks running
            if ($failed) {
                throw new \RuntimeException('Failed');
            }

            // FIXME: This is a messy way to do it
            if ($cancelled) {
                throw new \RuntimeException('Cancelled');
            }
        }
    }

    /**
     * Sends the files needed to the server.
     *
     * @param  DeployStep $step
     * @param  Server     $server
     * @param  ServerLog  $log
     * @return void
     */
    private function sendFilesForStep(DeployStep $step, Server $server, ServerLog $log)
    {
        $project = $this->deployment->project;

        $root_dir = preg_replace('#/$#', '', $server->path);

        $releases_dir = $root_dir . '/releases';

        $latest_release_dir = $releases_dir . '/' . $this->release_id;
        $release_shared_dir = $root_dir . '/shared';
        $remote_archive     = $root_dir . '/' . $project->id . '_' . $this->release_id . '.tar.gz';

        if ($step->stage === Stage::DO_CLONE) {
            $this->sendFile($this->release_archive, $remote_archive, $server, $log);
        } elseif ($step->stage === Stage::DO_INSTALL) {
            foreach ($project->projectFiles as $file) {
                $filepath = $latest_release_dir . '/' . $file->path;

                $this->sendFileFromString($server, $filepath, $file->content, $log);
            }
        }
    }

    /**
     * Generates the actual bash commands to run on the server.
     *
     * @param  DeployStep $step
     * @param  Server     $server
     * @param  ServerLog  $log
     * @return string
     */
    private function buildScript(DeployStep $step, Server $server, ServerLog $log)
    {
        $project = $this->deployment->project;

        $root_dir = preg_replace('#/$#', '', $server->path);

        // Precaution to make sure nothing accidentially runs at /
        if (empty($root_dir)) {
            return '';
        }

        $releases_dir = $root_dir . '/releases';

        $latest_release_dir = $releases_dir . '/' . $this->release_id;
        $release_shared_dir = $root_dir . '/shared';
        $remote_archive     = $root_dir . '/' . $project->id . '_' . $this->release_id . '.tar.gz';

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
            'release'         => $this->release_id,
            'release_path'    => $latest_release_dir,
            'project_path'    => $root_dir,
            'branch'          => $this->deployment->branch,
            'sha'             => $this->deployment->commit,
            'short_sha'       => $this->deployment->short_commit,
            'deployer_email'  => $deployer_email,
            'deployer_name'   => $deployer_name,
            'committer_email' => $this->deployment->committer_email,
            'committer_name'  => $this->deployment->committer,
        ];

        if (!$step->isCustomStep()) {
            $tokens = array_merge($tokens, [
                'remote_archive' => $remote_archive,
                'include_dev'    => $project->include_dev,
                'builds_to_keep' => $project->builds_to_keep + 1,
                'shared_path'    => $release_shared_dir,
                'releases_path'  => $releases_dir,
            ]);
        }

        // Generate the export
        $script = '';
        foreach ($project->variables as $variable) {
            $key   = $variable->name;
            $value = $variable->value;

            $script .= "export {$key}={$value}" . PHP_EOL;
        }

        // Now get the full scrip
        $script .= PHP_EOL . $this->getScriptForStep($step, $tokens);

        return trim($script);
    }

    /**
     * Generates an error string to log to the DB.
     *
     * @param  string $message
     * @return string
     */
    private function logError($message)
    {
        return '<error>' . $message . '</error>';
    }

    /**
     * Generates an general output string to log to the DB.
     *
     * @param  string $message
     * @return string
     */
    private function logSuccess($message)
    {
        return '<info>' . $message . '</info>';
    }

    /**
     * Gets the script which is used for the supplied step.
     *
     * @param  DeployStep $step
     * @param  array      $tokens
     * @return string
     */
    private function getScriptForStep(DeployStep $step, array $tokens = [])
    {
        $parser = new ScriptParser;

        switch ($step->stage) {
            case Stage::DO_CLONE:
                return $parser->parseFile('deploy.steps.CreateNewRelease', $tokens);
            case Stage::DO_INSTALL:
                // Write configuration file to release dir, symlink shared files and run composer
                return $this->configurationFileCommands($tokens['release_path']) .
                       $parser->parseFile('deploy.steps.InstallComposerDependencies', $tokens) .
                       $this->shareFileCommands($tokens['release_path'], $tokens['shared_path']);
            case Stage::DO_ACTIVATE:
                return $parser->parseFile('deploy.steps.ActivateNewRelease', $tokens);
            case Stage::DO_PURGE:
                return $parser->parseFile('deploy.steps.PurgeOldReleases', $tokens);
        }

        // Custom step
        return $parser->parseString($step->command->script, $tokens);
    }

    /**
     * Generates the SSH command for running the script on a server.
     *
     * @param  Server      $server
     * @param  string      $script
     * @param  string|null $user
     * @return string
     */
    private function generateSSHCommand(Server $server, $script, $user = null)
    {
        if (is_null($user)) {
            $user = $server->user;
        }

        if (config('app.debug')) {
            // Turn on verbose output so we can see all commands when in debug mode
            $script = 'set -v' . PHP_EOL . $script;
        }

        // Turn on quit on non-zero exit
        $script = 'set -e' . PHP_EOL . $script;

        return with(new ScriptParser)->parseFile('RunScriptOverSSH', [
            'private_key' => $this->private_key,
            'username'    => $user,
            'port'        => $server->port,
            'ip_address'  => $server->ip_address,
            'script'      => $script,
        ]);
    }

    /**
     * Sends a file to a remote server.
     *
     * @param  string           $local_file
     * @param  string           $remote_file
     * @param  Server           $server
     * @param  ServerLog        $log
     * @throws RuntimeException
     * @return void
     */
    private function sendFile($local_file, $remote_file, Server $server, $log)
    {
        $copy = sprintf(
            'rsync --verbose --compress --progress --out-format="Receiving %%n" -e "ssh -p %s ' .
            '-o CheckHostIP=no ' .
            '-o IdentitiesOnly=yes ' .
            '-o StrictHostKeyChecking=no ' .
            '-o PasswordAuthentication=no ' .
            '-i %s" ' .
            '%s %s@%s:%s',
            $server->port,
            $this->private_key,
            $local_file,
            $server->user,
            $server->ip_address,
            $remote_file
        );

        $process = new Process($copy);
        $process->setTimeout(null);
        ///$process->run();

        $output = '';
        $process->run(function ($type, $output_line) use (&$output, &$log) {
            if ($type === Process::ERR) {
                $output .= $this->logError($output_line);
            } else {
                // FIXME: Horrible hack
                $output_line = str_replace('received', 'xxx', $output_line);
                $output_line = str_replace('sent', 'received', $output_line);
                $output_line = str_replace('xxx', 'sent', $output_line);

                $output .= $this->logSuccess($output_line);
            }

            $log->output = $output;
            $log->save();
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        //return $process->getOutput();
    }

    /**
     * Send a string to server.
     *
     * @param  Server    $server      target server
     * @param  string    $remote_path remote filename
     * @param  string    $content     the file content
     * @param  ServerLog $log
     * @return void
     */
    private function sendFileFromString(Server $server, $remote_path, $content, ServerLog $log)
    {
        $tmp_file = tempnam(storage_path('app/'), 'tmpfile');
        file_put_contents($tmp_file, $content);

        // Upload the file
        $this->sendFile($tmp_file, $remote_path, $server, $log);

        unlink($tmp_file);
    }

    /**
     * create the command for sending uploaded files.
     *
     * @param  string $release_dir
     * @return string
     */
    private function configurationFileCommands($release_dir)
    {
        $commands = [];

        foreach ($this->deployment->project->projectFiles as $file) {
            $filepath = $release_dir . '/' . $file->path;

            $commands[] = sprintf('chmod 0664 %s', $filepath);
        }

        return implode(PHP_EOL, $commands) . PHP_EOL;
    }

    /**
     * create the command for share files.
     *
     * @param  string $release_dir
     * @param  string $shared_dir
     * @return string
     */
    private function shareFileCommands($release_dir, $shared_dir)
    {
        $commands = [];

        foreach ($this->deployment->project->sharedFiles as $filecfg) {
            if ($filecfg->file) {
                $pathinfo = pathinfo($filecfg->file);
                $isDir    = false;

                if (substr($filecfg->file, 0, 1) === '/') {
                    $filecfg->file = substr($filecfg->file, 1);
                }

                if (substr($filecfg->file, -1) === '/') {
                    $isDir         = true;
                    $filecfg->file = substr($filecfg->file, 0, -1);
                }

                if (isset($pathinfo['extension'])) {
                    $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
                } else {
                    $filename = $pathinfo['filename'];
                }

                $sourceFile = $shared_dir . '/' . $filename;
                $targetFile = $release_dir . '/' . $filecfg->file;

                if ($isDir) {
                    $commands[] = sprintf(
                        '[ -d %s ] && cp -pRn %s %s && rm -rf %s',
                        $targetFile,
                        $targetFile,
                        $sourceFile,
                        $targetFile
                    );
                    $commands[] = sprintf('[ ! -d %s ] && mkdir %s', $sourceFile, $sourceFile);
                } else {
                    $commands[] = sprintf(
                        '[ -f %s ] && cp -pRn %s %s && rm -rf %s',
                        $targetFile,
                        $targetFile,
                        $sourceFile,
                        $targetFile
                    );
                    $commands[] = sprintf('[ ! -f %s ] && touch %s', $sourceFile, $sourceFile);
                }

                $commands[] = sprintf('ln -s %s %s', $sourceFile, $targetFile);
            }
        }

        return implode(PHP_EOL, $commands) . PHP_EOL;
    }
}
