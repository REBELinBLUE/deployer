<?php namespace App\Commands;

use Config;
use Queue;
use App\Deployment;
use App\DeployStep;
use App\ServerLog;
use App\Server;
use App\Command as Stage;
use App\Project;
use App\Commands\Command;
use App\Events\DeployFinished;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Symfony\Component\Process\Process;

/**
 * Deploys an actual project
 * TODO: rewrite this as it is doing way too much and is very messy now
 */
class DeployProject extends Command implements SelfHandling, ShouldBeQueued
{
    use InteractsWithQueue, SerializesModels;

    private $deployment;
    private $private_key;

    /**
     * Create a new command instance.
     *
     * @param Deployment $deployment
     * @return DeployProject
     */
    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
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
        $this->deployment->status = Deployment::DEPLOYING;
        $this->deployment->save();

        $project->status = Project::DEPLOYING;
        $project->save();

        $this->private_key = tempnam(storage_path() . '/app/', 'sshkey');
        file_put_contents($this->private_key, $project->private_key);

        try {
            // If the build has been manually triggered update the git information from the remote repository
            if ($this->deployment->commit == Deployment::LOADING) {
                $this->updateRepoInfo();
            }

            foreach ($this->deployment->steps as $step) {
                $this->runStep($step);
            }

            $this->deployment->status = Deployment::COMPLETED;
            $project->status = Project::FINISHED;
        } catch (\Exception $error) {
            $this->deployment->status = Deployment::FAILED;
            $project->status = Project::FAILED;

            $this->cancelPendingSteps($this->deployment->steps);

            if (isset($step)) {
                // Cleanup the release if it has not been activated
                if ($step->stage <= Stage::DO_ACTIVATE) {
                    $this->cleanupDeployment();
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
    }

    /**
     * Clones the repository locally to get the latest log entry and updates
     * the deployment model
     *
     * @return void
     * TODO: Change this to use the Gitlab API
     */
    private function updateRepoInfo()
    {
        $wrapper = tempnam(storage_path() . '/app/', 'gitssh');
        file_put_contents($wrapper, $this->gitWrapperScript($this->private_key));

        $workingdir = tempnam(storage_path() . '/app/', 'clone');
        unlink($workingdir);

        $cmd = <<< CMD
chmod +x "{$wrapper}" && \
export GIT_SSH="{$wrapper}" && \
git clone --quiet --branch %s --depth 1 %s {$workingdir} && \
cd {$workingdir} && \
git checkout %s --quiet && \
git log --pretty=format:"%%H%%x09%%an" && \
rm -rf {$workingdir}
CMD;

        $process = new Process(sprintf(
            $cmd,
            $this->deployment->branch,
            $this->deployment->project->repository,
            $this->deployment->branch
        ));

        $process->setTimeout(null);
        $process->run();

        unlink($wrapper);

        if (!$process->isSuccessful()) {
            // FIXME: Handle this situation as it is then unclear what went wrong
            throw new \RuntimeException('Could not get repository info - ' . $process->getErrorOutput());
        }

        $git_info = $process->getOutput();

        $parts = explode("\x09", $git_info);
        $this->deployment->commit    = $parts[0];
        $this->deployment->committer = trim($parts[1]);
        $this->deployment->save();
    }

    /**
     * Removed left over artifacts from a failed deploy on each server
     *
     * @return void
     * TODO: Clean this up as there is some duplication with getScript()
     */
    private function cleanupDeployment()
    {
        $project = $this->deployment->project;

        $release_id = date('YmdHis', strtotime($this->deployment->started_at));

        foreach ($project->servers as $server) {
            if (!$server->deploy_code) {
                continue;
            }

            $root_dir = preg_replace('#/$#', '', $server->path);

            if (empty($root_dir)) {
                continue;
            }

            $releases_dir = $root_dir . '/releases';
            $latest_release_dir = $releases_dir . '/' . $release_id;

            $remote_key_file = $root_dir . '/id_rsa';
            $remote_wrapper_file = $root_dir . '/wrapper.sh';

            $commands = [
                sprintf('cd %s', $root_dir),
                sprintf('[ -f %s ] && rm %s', $remote_key_file, $remote_key_file),
                sprintf('[ -f %s ] && rm %s', $remote_wrapper_file, $remote_wrapper_file),
                sprintf('[ -d %s ] && rm -rf %s', $latest_release_dir, $latest_release_dir)
            ];

            $script = implode(PHP_EOL, $commands);

            $process = new Process($this->sshCommand($server, $script));
            $process->setTimeout(null);
            $process->run();
        }
    }

    /**
     * Finds all pending steps and marks them as cancelled
     *
     * @return void
     */
    private function cancelPendingSteps()
    {
        foreach ($this->deployment->steps as $step) {
            foreach ($step->servers as $log) {
                if ($log->status == ServerLog::PENDING) {
                    $log->status = ServerLog::CANCELLED;
                    $log->save();
                }
            }
        }
    }

    /**
     * Executes the commands for a step
     *
     * @param DeployStep $step
     * @return void
     * @throws \RuntimeException
     * TODO: Remove build on failure
     */
    private function runStep(DeployStep $step)
    {
        foreach ($step->servers as $log) {
            $log->status = ServerLog::RUNNING;
            $log->started_at = date('Y-m-d H:i:s');
            $log->save();

            $prefix = $step->stage;
            if ($step->command) {
                $prefix = $step->command->name;
            }

            try {
                $server = $log->server;
                $script = $this->getScript($step, $server);

                $user = $server->user;
                if (isset($step->command)) {
                    $user = $step->command->user;
                }

                $log->script = $script;

                $failed = false;

                if (!empty($script)) {
                    $process = new Process($this->sshCommand($server, $script, $user));
                    $process->setTimeout(null);

                    $output = '';
                    $process->run(function ($type, $output_line) use (&$output, &$log) {
                        if ($type == Process::ERR) {
                            $output .= $this->logError($output_line);
                        } else {
                            $output .= $this->logSuccess($output_line);
                        }

                        $log->output = $output;
                        $log->save();
                    });

                    if (!$process->isSuccessful()) {
                        $failed = true;
                    }

                    $log->output = $output;
                }
            } catch (\Exception $e) {
                $msg = '[' . $server->ip_address . ']:' . $e->getMessage();
                $log->output .= $this->logError($msg);
                $failed = true;
            }

            $log->status = $failed ? ServerLog::FAILED : ServerLog::COMPLETED;
            $log->finished_at = date('Y-m-d H:i:s');
            $log->save();

            // Throw an exception to prevent any more tasks running
            if ($failed) {
                throw new \RuntimeException('Failed!');
            }
        }
    }

    /**
     * Generates the actual bash commands to run on the server
     *
     * @param DeployStep $step
     * @param Server     $server
     * @return string
     */
    private function getScript(DeployStep $step, Server $server)
    {
        $project = $this->deployment->project;

        $root_dir = preg_replace('#/$#', '', $server->path);

        // Precaution to make sure nothing accidentially runs at /
        if (empty($root_dir)) {
            return '';
        }

        $releases_dir = $root_dir . '/releases';

        $release_id = date('YmdHis', strtotime($this->deployment->started_at));
        $latest_release_dir = $releases_dir . '/' . $release_id;
        $release_shared_dir = $root_dir . '/shared';

        $commands = false;

        if ($step->stage === Stage::DO_CLONE) { // Clone the repository
            $remote_key_file = $root_dir . '/id_rsa';
            $remote_wrapper_file = $root_dir . '/wrapper.sh';

            // FIXME: This does not belong here as this function should
            // only being returning the commands
            // not running them!
            $this->prepareServer($server);

            $commands = [
                sprintf('cd %s', $root_dir),
                sprintf('chmod 0600 %s', $remote_key_file),
                sprintf('chmod +x %s', $remote_wrapper_file),
                sprintf('[ ! -d %s ] && mkdir %s', $releases_dir, $releases_dir),
                sprintf('[ ! -d %s ] && mkdir %s', $release_shared_dir, $release_shared_dir),
                sprintf('cd %s', $releases_dir),
                sprintf('export GIT_SSH="%s"', $remote_wrapper_file),
                sprintf(
                    'git clone --branch %s --depth 1 --recursive %s %s',
                    $this->deployment->branch,
                    $project->repository,
                    $latest_release_dir
                ),
                sprintf('cd %s', $latest_release_dir),
                sprintf('git checkout %s', $this->deployment->branch),
                sprintf('rm %s %s', $remote_key_file, $remote_wrapper_file)
            ];
        } elseif ($step->stage === Stage::DO_INSTALL) { // Install composer dependencies
            $commands = [
                sprintf('cd %s', $latest_release_dir),
                sprintf(
                    '[ -f %s/composer.json ] && composer install --no-interaction --optimize-autoloader ' .
                    '--no-dev --prefer-dist --no-ansi --working-dir "%s"',
                    $latest_release_dir,
                    $latest_release_dir
                )
            ];

            // the shared file must be created in the install step
            $shareFileCommands = $this->shareFileCommands(
                $project,
                $latest_release_dir,
                $release_shared_dir
            );

            $commands = array_merge($commands, $shareFileCommands);

            // write project file to release dir before install
            
            $projectFiles = $project->projectFiles;
            foreach ($projectFiles as $file) {
                if ($file->path) {
                    $filepath = $latest_release_dir . '/' . $file->path;
                    $this->sendFileFromString($server, $filepath, $file->content);
                }
            }
        } elseif ($step->stage === Stage::DO_ACTIVATE) { // Activate latest release
            $commands = [
                sprintf('cd %s', $root_dir),
                sprintf('[ -h %s/latest ] && rm %s/latest', $root_dir, $root_dir),
                sprintf('ln -s %s %s/latest', $latest_release_dir, $root_dir)
            ];
        } elseif ($step->stage === Stage::DO_PURGE) { // Purge old releases
            $commands = [
                sprintf('cd %s', $releases_dir),
                sprintf('(ls -t|head -n %u;ls)|sort|uniq -u|xargs rm -rf', $project->builds_to_keep + 1)
            ];
        } else { // Custom step!
            $commands = $step->command->script;

            $tokens = [
                '{{ release }}'         => $release_id,
                '{{ release_path }}'    => $latest_release_dir,
                '{{ project_path }}'    => $root_dir,
                '{{ sha }}'             => $this->deployment->commit,
                '{{ short_sha }}'       => $this->deployment->shortCommit()
            ];

            $commands = str_replace(array_keys($tokens), array_values($tokens), $commands);
        }

        if (is_array($commands)) {
            $commands = implode(PHP_EOL, $commands);
        }

        return $commands;
    }

    /**
     * Generates an error string to log to the DB
     *
     * @param string $message
     * @return string
     */
    private function logError($message)
    {
        return '<error>' . $message . '</error>';
    }

    /**
     * Generates an general output string to log to the DB
     *
     * @param string $message
     * @return string
     */
    private function logSuccess($message)
    {
        return '<info>' . $message . '</info>';
    }

    /**
     * Generates the SSH command for running the script on a server
     *
     * @param Server $server
     * @param string $script The script to run
     * @param string $user
     * @return string
     */
    private function sshCommand(Server $server, $script, $user = null)
    {
        if (is_null($user)) {
            $user = $server->user;
        }

        $script = 'set -e' . PHP_EOL . $script;
        return 'ssh -o CheckHostIP=no \
                 -o IdentitiesOnly=yes \
                 -o StrictHostKeyChecking=no \
                 -o PasswordAuthentication=no \
                 -o IdentityFile=' . $this->private_key . ' \
                 -p ' . $server->port . ' \
                 ' . $user . '@' . $server->ip_address . ' \'bash -s\' << EOF
                 '.$script.'
EOF';

    }

    /**
     * Generates the content of a git bash script
     *
     * @param string $key_file_path The path to the public key to use
     * @return string
     */
    private function gitWrapperScript($key_file_path)
    {
        return <<<OUT
#!/bin/sh
ssh -o CheckHostIP=no \
    -o IdentitiesOnly=yes \
    -o StrictHostKeyChecking=no \
    -o PasswordAuthentication=no \
    -o IdentityFile={$key_file_path} $*

OUT;
    }

    /**
     * Sends a file to a remote server
     *
     * @param string $local_file
     * @param string $remote_file
     * @param Server $server
     * @return void
     * @throws RuntimeException
     */
    private function sendFile($local_file, $remote_file, Server $server)
    {
        $copy = sprintf(
            'scp -o CheckHostIP=no ' .
            '-o IdentitiesOnly=yes ' .
            '-o StrictHostKeyChecking=no ' .
            '-o PasswordAuthentication=no ' .
            '-i %s %s %s@%s:%s',
            $this->private_key,
            $local_file,
            $server->user,
            $server->ip_address,
            $remote_file
        );

        $process = new Process($copy);
        $process->setTimeout(null);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Could not send file - ' . $process->getErrorOutput());
        }
    }

    /**
     * Prepares a server for code deployment by adding the files which are required
     *
     * @param Server $server
     * @return void
     */
    private function prepareServer(Server $server)
    {
        $root_dir = preg_replace('#/$#', '', $server->path);

        $remote_key_file = $root_dir . '/id_rsa';
        $remote_wrapper_file = $root_dir . '/wrapper.sh';

        // Upload the SSH private key
        $this->sendFile($this->private_key, $remote_key_file, $server);

        // Upload the wrapper file
        $this->sendFileFromString(
            $server,
            $remote_wrapper_file,
            $this->gitWrapperScript($remote_key_file)
        );
    }

    /**
     * send a string to server
     *
     * @param  Server $server   target server
     * @param  string $filename remote filename
     * @param  string $content  the file content
     * @return void
     */
    private function sendFileFromString(Server $server, $filepath, $content)
    {
        $wrapper = tempnam(storage_path() . '/app/', 'tmpfile');
        file_put_contents($wrapper, $content);

        // Upload the wrapper file
        $this->sendFile($wrapper, $filepath, $server);

        unlink($wrapper);
    }

    /**
     * create the command for share files
     *
     * @param  Project $project     the related project
     * @param  string  $release_dir current release dir
     * @param  string  $shared_dir  the shared dir
     * @return array
     */
    private function shareFileCommands(Project $project, $release_dir, $shared_dir)
    {
        $commands = array();
        foreach ($project->shareFiles as $filecfg) {
            if ($filecfg->file) {
                $pathinfo = pathinfo($filecfg->file);
                $isDir = false;

                if (substr($filecfg->file, 0, 1) == '/') {
                    $filecfg->file = substr($filecfg->file, 1);
                }

                if (substr($filecfg->file, -1) == '/') {
                    $isDir = true;
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
        return $commands;
    }
}
