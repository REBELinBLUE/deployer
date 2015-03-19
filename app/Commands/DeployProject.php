<?php namespace App\Commands;

use App\Commands\Command;

use App\Deployment;
use App\DeployStep;
use App\Server;

use Config;
use SSH;

use Symfony\Component\Process\Process;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class DeployProject extends Command implements SelfHandling, ShouldBeQueued
{
    use InteractsWithQueue, SerializesModels;

    private $deployment;
    private $private_key;

    /**
     * Create a new command instance.
     *
     * @return void
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
        $this->deployment->status = 'Deploying';
        $this->deployment->save();

        $project->status = 'Deploying';
        $project->save();

        $this->private_key = tempnam(storage_path() . '/app/', 'sshkey');
        file_put_contents($this->private_key, $project->private_key);

        try {
            // If the build has been manually triggered updated the git information from the remote repository
            if ($this->deployment->commit == 'Loading') {
                $this->updateRepoInfo();
            }

            foreach ($this->deployment->steps as $step) {
                $this->runStep($step);
            }

            $this->deployment->status = 'Completed';
            $project->status = 'Finished';
        } catch (\Exception $error) {
            $this->deployment->status = 'Failed';
            $project->status = 'Failed';

            $this->cancelPendingSteps($this->deployment->steps);
        }

        $this->deployment->finished_at = date('Y-m-d H:i:s');
        $this->deployment->save();

        $project->last_run = date('Y-m-d H:i:s');
        $project->save();

        unlink($this->private_key);
    }

    private function configureServers()
    {
        foreach ($this->deployment->project->servers as $server) {
            Config::set('remote.connections.server' . $server->id . '.host', $server->ip_address);
            Config::set('remote.connections.server' . $server->id . '.username', $server->user);
            Config::set('remote.connections.server' . $server->id . '.password', '');
            Config::set('remote.connections.server' . $server->id . '.key', $this->private_key);
            Config::set('remote.connections.server' . $server->id . '.keyphrase', '');
            Config::set('remote.connections.server' . $server->id . '.root', $server->path);
        }
    }

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
git log --pretty=format:"%%h%%x09%%an" && \
rm -rf {$workingdir}
CMD;

        $this->outputToConsole('Checking repository state' . PHP_EOL);
        $process = new Process(sprintf($cmd, $this->deployment->project->branch, $this->deployment->project->repository, $this->deployment->project->branch));
        $process->setTimeout(null);
        $process->run();

        unlink($wrapper);

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Could not get repository info');
        }

        $git_info = $process->getOutput();

        $this->deployment->commit    = substr($git_info, 0, 7);
        $this->deployment->committer = trim(substr($git_info, 7));
        $this->deployment->save();
    }

    private function cancelPendingSteps()
    {
        foreach ($this->deployment->steps as $step) {
            foreach ($step->servers as $log) {
                if ($log->status == 'Pending') {
                    $log->status = 'Cancelled';
                    $log->save();
                }
            }
        }
    }

    private function runStep(DeployStep $step)
    {
        foreach ($step->servers as $log) {
            $log->status = 'Running';
            $log->started_at = date('Y-m-d H:i:s');
            $log->save();

            $log->status = 'Completed';

            $prefix = $step->stage;
            if ($step->command) {
                $prefix = $step->command->name;
            }

            $this->outputToConsole($prefix . ' on ' . $log->server->name . ' (' . $log->server->ip_address . ')' . PHP_EOL);

            $server = $log->server;
            $script = $this->getScript($step, $server);

            $user = $server->user;
            if (isset($step->command)) {
                $user = $step->command->user;
            }

            $failed = false;

            if (!empty($script)) {
                $script = 'set -e' . PHP_EOL . $script;
                $process = new Process(
                    'ssh -o CheckHostIP=no -o IdentitiesOnly=yes -o StrictHostKeyChecking=no -o PasswordAuthentication=no -o IdentityFile=' . $this->private_key . ' ' . $user . '@' . $server->ip_address . ' \'bash -s\' << EOF
'.$script.'
EOF'
                );
                $process->setTimeout(null);

                $output = '';
                $process->run(function ($type, $output_line) use (&$output) {
                    if ($type == Process::ERR) {
                        $output .= $this->logError($output_line);
                    } else {
                        $output .= $this->logSuccess($output_line);
                    }
                });

                if (!$process->isSuccessful()) {
                    $log->status = 'Failed';
                    $failed = true;
                }

                $log->output = $output;
            }

            $log->finished_at = date('Y-m-d H:i:s');
            $log->save();

            // Throw an exception to prevent any more tasks running
            if ($failed) {
                throw new \RuntimeException('Failed!');
            }
        }
    }

    private function getScript(DeployStep $step, Server $server)
    {
        $project = $this->deployment->project;

        $root_dir = preg_replace('#/$#', '', $server->path); # Remove any trailing slash from the path on the server
        $releases_dir = $root_dir . '/releases';

        $release_id = date('YmdHis', strtotime($this->deployment->started_at));
        $latest_release_dir = $releases_dir . '/' . $release_id;

        $commands = false;

        if ($step->stage == 'Clone') { // Clone the repository
            $remote_key_file = $root_dir . '/id_rsa';
            $remote_wrapper_file = $root_dir . '/wrapper.sh';

            $this->configureServers();

            $server_name = 'server' . $server->id;

            // Upload the files we need
            // FIXME: See if we can find a way around this as we have the entire SSH package just for this
            SSH::into($server_name)->putString($remote_key_file, $project->private_key);
            SSH::into($server_name)->putString($remote_wrapper_file, $this->gitWrapperScript($remote_key_file));

            $commands = [
                sprintf('cd %s', $root_dir),
                sprintf('chmod 0600 %s', $remote_key_file),
                sprintf('chmod +x %s', $remote_wrapper_file),
                sprintf('[ ! -d %s ] && mkdir %s', $releases_dir, $releases_dir),
                sprintf('cd %s', $releases_dir),
                sprintf('export GIT_SSH="%s"', $remote_wrapper_file),
                sprintf('git clone --branch %s --depth 1 %s %s', $project->branch, $project->repository, $latest_release_dir),
                sprintf('cd %s', $latest_release_dir),
                sprintf('git checkout %s', $project->branch),
                sprintf('rm %s %s', $remote_key_file, $remote_wrapper_file)
            ];
        } elseif ($step->stage == 'Install') { // Install Composer dependencies
            $commands = [
                sprintf('cd %s', $latest_release_dir),
                'composer install'
            ];
        } elseif ($step->stage == 'Activate') { // Activate latest release
            $commands = [
                sprintf('cd %s', $root_dir),
                sprintf('[ -h %s/latest ] && rm %s/latest', $root_dir, $root_dir),
                sprintf('ln -s %s %s/latest', $latest_release_dir, $root_dir)
            ];
        } elseif ($step->stage == 'Purge') { // Purge old releases
            $commands = [
            ];
        } else { // Custom step!
            $commands = $step->command->script;

            $commands = str_replace('{{ release }}', $release_id, $commands);
            $commands = str_replace('{{ release_path }}', $latest_release_dir, $commands);
        }

        if (is_array($commands)) {
            $commands = implode(PHP_EOL, $commands);
        }

        return $commands;
    }


    private function logError($message)
    {
        $this->outputToConsole("\033[0;31m" . $message .  "\033[0m");
        return '<error>' . $message . '</error>';
    }

    private function logSuccess($message)
    {
        //$this->outputToConsole("\033[0;32m" . $message .  "\033[0m");
        //
        $this->outputToConsole($message);
        return '<info>' . $message . '</info>';
    }

    private function outputToConsole($message)
    {
        // FIXME: Only output in debug mode
        echo 'Deployment #' . $this->deployment->id . ': '  . $message;
    }

    private function gitWrapperScript($key_file_path)
    {
        return <<<OUT
#!/bin/sh
ssh -o CheckHostIP=no -o IdentitiesOnly=yes -o StrictHostKeyChecking=no -o PasswordAuthentication=no -o IdentityFile={$key_file_path} $*

OUT;
    }
}
