<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use REBELinBLUE\Deployer\Jobs\Job;
use REBELinBLUE\Deployer\Server;
use Symfony\Component\Process\Process;
use REBELinBLUE\Deployer\Scripts\Parser as ScriptParser;

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
            $process = new Process($this->sshCommand($this->server, $key));
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
        $parser = new ScriptParser;

        $script = $parser->parseFile('TestServerConnection', [
            'project_path'   => $server->path,
            'test_file'      => time() . '_testing_deployer.txt',
            'test_directory' => time() . '_testing_deployer_dir'
        ]);



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
