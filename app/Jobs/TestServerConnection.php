<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Server;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

/**
 * Tests if a server can successfully be SSHed into.
 */
class TestServerConnection extends Job implements SelfHandling, ShouldQueue
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

        $key = tempnam(storage_path() . '/app/', 'sshkey');
        file_put_contents($key, $this->server->project->private_key);

        try {
            $command = $this->sshCommand($this->server, $key, 'ls');
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
     * Generates the SSH command for running the script on a server.
     *
     * @param  Server $server
     * @param  string $script The script to run
     * @return string
     */
    private function sshCommand(Server $server, $private_key, $script)
    {
        $script = 'set -e' . PHP_EOL . $script;

        return 'ssh -o CheckHostIP=no \
                 -o IdentitiesOnly=yes \
                 -o StrictHostKeyChecking=no \
                 -o PasswordAuthentication=no \
                 -o IdentityFile=' . $private_key . ' \
                 -p ' . $server->port . ' \
                 ' . $server->user . '@' . $server->ip_address . ' \'bash -s\' << EOF
                 ' . $script . '
EOF';
    }
}
