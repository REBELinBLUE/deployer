<?php namespace App\Commands;

use App\Commands\Command;

use App\Deployment;

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

        $key = tempnam(storage_path() . '/app/', 'sshkey');
        file_put_contents($key, $project->private_key);

        $this->configureServers($key);

        try
        {
            $this->updateRepoInfo($key); // FIXME: We should be able to get this same info from the clone step

            foreach (['clone', 'install', 'activate', 'purge'] as $command)
            {
                foreach ($servers as $server)
                {
                    if ($command != 'clone') {
                        continue;
                    }

                    $server->label = 'server' . $server->id;

                    $keyfile = $server->path . '/id_rsa';
                    $wrapperfile = $server->path . '/wrapper.sh';
                    $release = date('YmdHis');

                    $releases = $server->path . '/releases/';

                    $wrapperscript = <<<OUT
#!/bin/sh
ssh -o CheckHostIP=no -o IdentitiesOnly=yes -o StrictHostKeyChecking=no -o PasswordAuthentication=no -o IdentityFile={$keyfile} $*

OUT;

                    // Upload the files we need
                    SSH::into($server->label)->putString($keyfile, $project->private_key);
                    SSH::into($server->label)->putString($wrapperfile, $wrapperscript);

                    $cmds = [
                        sprintf('cd %s', $server->path),
                        sprintf('chmod 0600 %s', $keyfile),
                        sprintf('chmod +x %s', $wrapperfile),
                        sprintf('mkdir --parents %s', $releases),
                        sprintf('cd %s 2>&1', $releases),
                        sprintf('export GIT_SSH="%s"', $wrapperfile),
                        sprintf('git clone --branch %s --depth 1 %s %s', $project->branch, $project->repository, $releases . $release),
                        sprintf('cd %s', $releases . $release),
                        sprintf('git checkout %s', $project->branch),
                        sprintf('rm %s %s', $keyfile, $wrapperfile),
                        sprintf('composer install')
                    ];

                    $script = 'set -e' . PHP_EOL . implode(' 2>&1 && ', $cmds) . ' 2>&1';

                    $process = new Process(
                        'ssh -o CheckHostIP=no -o IdentitiesOnly=yes -o StrictHostKeyChecking=no -o PasswordAuthentication=no -o IdentityFile=' . $key . ' ' . $server->user . '@' . $server->ip_address . ' \'bash -s\' << EOF
'.$script.'
EOF'
                    );

                    $process->run();

                    if (!$process->isSuccessful()) {
                        throw new \RuntimeException($process->getErrorOutput());
                    }

                    $log = $process->getOutput();

                    // FIXME: Handle errors

                    echo $log;

                }
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

        unlink($key);
    }

    private function configureServers($key)
    {
        foreach ($this->deployment->project->servers as $server)
        {
            Config::set('remote.connections.server' . $server->id . '.host', $server->ip_address);
            Config::set('remote.connections.server' . $server->id . '.username', $server->user);
            Config::set('remote.connections.server' . $server->id . '.password', '');
            Config::set('remote.connections.server' . $server->id . '.key', $key);
            Config::set('remote.connections.server' . $server->id . '.keyphrase', '');
            Config::set('remote.connections.server' . $server->id . '.root', $server->path);
        }
    }

    private function updateRepoInfo($key)
    {
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

        $process = new Process(sprintf($cmd, $this->deployment->project->branch, $this->deployment->project->repository, $this->deployment->project->branch));
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $git_info = $process->getOutput();

        $this->deployment->commit    = substr($git_info, 0, 7);
        $this->deployment->committer = trim(substr($git_info, 7));
        $this->deployment->save();

        unlink($wrapper);
    }
}
