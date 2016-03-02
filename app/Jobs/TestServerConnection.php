<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use REBELinBLUE\Deployer\Jobs\Job;
use REBELinBLUE\Deployer\Server;
use Symfony\Component\Process\Process;

/**
 * Tests if a server can successfully be SSHed into.
 */
class TestServerConnection extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $server;

    /**
     * Create a new command instance.
     *
     * @param  Server               $server
     * @return TestServerConnection
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->server->status = Server::TESTING;
        $this->server->save();

        $key = tempnam(storage_path('app/'), 'sshkey');
        file_put_contents($key, $this->server->project->private_key);

        try {
            $command = $this->sshCommand($this->server, $key);
            $process = new Process($command);
            $process->setTimeout(null);
            $process->run();

            if (!$process->isSuccessful()) {
                $this->server->status = Server::FAILED;
            } else {
                $this->server->status = Server::SUCCESSFUL;
            }
        } catch (\Exception $error) {
            $this->server->status = Server::FAILED;
        }

        $this->server->save();

        unlink($key);
    }

    /**
     * Generates the script to test the test.
     *
     * @param  Server $server
     * @param  string $private_key
     * @return string
     */
    private function sshCommand(Server $server, $private_key)
    {
        $tmpfile = time() . '_testing_deployer.txt';
        $tmpdir  = time() . '_testing_deployer_dir';

        // Ensure the directory exists and can be written to
        // that directories can be made and files/directories
        // can be removed
        $script = <<< EOD
            set -e
            cd $server->path
            ls
            touch $tmpfile
            echo "testing" >> $tmpfile
            chmod +x $tmpfile
            rm $tmpfile
            mkdir $tmpdir
            touch $tmpdir/$tmpfile
            echo "testing" >> $tmpdir/$tmpfile
            chmod +x $tmpdir/$tmpfile
            ls $tmpdir/
            rm -rf $tmpdir
EOD;

        return 'ssh -o CheckHostIP=no \
                 -o IdentitiesOnly=yes \
                 -o StrictHostKeyChecking=no \
                 -o PasswordAuthentication=no \
                 -o IdentityFile=' . $private_key . ' \
                 -p ' . $server->port . ' \
                 ' . $server->user . '@' . $server->ip_address . ' \'bash -s\' << \'EOF\'
                 ' . $script . '
EOF';
    }
}
