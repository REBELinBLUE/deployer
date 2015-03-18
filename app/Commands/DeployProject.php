<?php namespace App\Commands;

use App\Commands\Command;

use App\Deployment;
use App\DeployStep;

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

    private $deployment, $private_key, $steps = [];

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
        $servers = $this->deployment->project->servers;

        $this->private_key = tempnam(storage_path() . '/app/', 'sshkey');
        file_put_contents($this->private_key, $project->private_key);

        $this->configureServers();

        try
        {
            $this->updateRepoInfo();

            foreach ($this->deployment->steps as $step)
            {
                $this->runStep($step);
            }

            $this->deployment->status = 'Completed';
            $project->status = 'Finished';
        }
        catch (\Exception $error)
        {
            echo $error;

            $this->deployment->status = 'Failed';
            $project->status = 'Failed';
        }

        $this->deployment->save();
        $project->save();

        unlink($this->private_key);
    }

    private function configureServers()
    {
        foreach ($this->deployment->project->servers as $server)
        {
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
        $key = $this->private_key;

        $script = <<<OUT
#!/bin/sh
ssh -o CheckHostIP=no -o IdentitiesOnly=yes -o StrictHostKeyChecking=no -o PasswordAuthentication=no -o IdentityFile={$key} $*

OUT;

        $wrapper = tempnam(storage_path() . '/app/', 'gitssh');
        file_put_contents($wrapper, $script);

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

        $this->log('Checking repository state' . PHP_EOL);
        $process = new Process(sprintf($cmd, $this->deployment->project->branch, $this->deployment->project->repository, $this->deployment->project->branch));
        $process->setTimeout(null);
        $process->run();

        if ($process->getExitCode() !== 0) {
            die('failed');
        }

        $git_info = $process->getOutput();

        $this->deployment->commit    = substr($git_info, 0, 7);
        $this->deployment->committer = trim(substr($git_info, 7));
        $this->deployment->save();

        unlink($wrapper);
    }

    private function runStep(DeployStep $command)
    {
        $project = $this->deployment->project;
        foreach ($project->servers as $server)
        {
            if ($command != 'Clone') {
                continue;
            }

            $server->label = 'server' . $server->id;

            $remote_key_file = $server->path . '/id_rsa';
            $remote_wrapper_file = $server->path . '/wrapper.sh';
            $release = date('YmdHis', strtotime($this->deployment->run));

            $releases = $server->path . '/releases/';

            $script = <<<OUT
#!/bin/sh
ssh -o CheckHostIP=no -o IdentitiesOnly=yes -o StrictHostKeyChecking=no -o PasswordAuthentication=no -o IdentityFile={$remote_key_file} $*

OUT;

            // Upload the files we need
            SSH::into($server->label)->putString($remote_key_file, $project->private_key);
            SSH::into($server->label)->putString($remote_wrapper_file, $script);

            $cmds = [
                sprintf('cd %s', $server->path),
                sprintf('chmod 0600 %s', $remote_key_file),
                sprintf('chmod +x %s', $remote_wrapper_file),
                sprintf('mkdir --parents %s', $releases),
                sprintf('cd %s 2>&1', $releases),
                sprintf('export GIT_SSH="%s"', $remote_wrapper_file),
                sprintf('git clone --branch %s --depth 1 %s %s', $project->branch, $project->repository, $releases . $release),
                sprintf('cd %s', $releases . $release),
                sprintf('git checkout %s', $project->branch),
                sprintf('rm %s %s', $remote_key_file, $remote_wrapper_file),
                sprintf('composer install'),
                sprintf('[ -h %s/latest ] && rm %s/latest', $server->path, $server->path),
                sprintf('ln -s %s %s/latest', $releases . $release, $server->path)
            ];

            $script = 'set -e' . PHP_EOL . implode(PHP_EOL, $cmds);
            $process = new Process(
                'ssh -o CheckHostIP=no -o IdentitiesOnly=yes -o StrictHostKeyChecking=no -o PasswordAuthentication=no -o IdentityFile=' . $this->private_key . ' ' . $server->user . '@' . $server->ip_address . ' \'bash -s\' << EOF
'.$script.'
EOF'
            );
            $process->setTimeout(null);

            $output = '';

            $this->log('Running clone' . PHP_EOL);
            $process->run(function ($type, $output_line) use (&$output) {
                if ($type == Process::ERR) {
                    $output .= $this->logError($output_line);
                }
                else {
                    $output .= $this->logSuccess($output_line);
                }
            });

            //$log->status = 'Completed';
            if ($process->getExitCode() !== 0) {
  //              $log->status = 'Failed';
            }

            //$log->output = $output;
            //$log->save();
        }
    }

    private function logError($message)
    {
        return $this->log('<warning>' . $message . '</warning>');
    }

    private function logSuccess($message)
    {
        return $this->log('<success>' . $message . '</success>');
    }

    private function log($message)
    {
        $console = str_replace('<warning>', "\033[0;31m", $message);
        $console = str_replace('</warning>', "\033[0m", $console);
        $console = str_replace('<success>', "\033[0;32m", $console);
        $console = str_replace('</success>', "\033[0m", $console);

        //echo $console;

        return $message;
    }
}
