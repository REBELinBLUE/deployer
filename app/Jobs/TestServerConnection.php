<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Scripts\Runner as Process;
use REBELinBLUE\Deployer\Server;

/**
 * Tests if a server can successfully be SSHed into.
 */
class TestServerConnection extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var Server
     */
    public $server;

    /**
     * TestServerConnection constructor.
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Execute the command.
     */
    public function handle()
    {
        $this->server->status = Server::TESTING;
        $this->server->save();

        $key = tempnam(storage_path('app/'), 'sshkey');
        file_put_contents($key, $this->server->project->private_key);

        try {
            $process = new Process('TestServerConnection', [
                'server_id'      => $this->server->id,
                'project_path'   => $this->server->clean_path,
                'test_file'      => time() . '_testing_deployer.txt',
                'test_directory' => time() . '_testing_deployer_dir',
            ]);

            $process->setServer($this->server, $key)
                    ->run();

            if (!$process->isSuccessful()) {
                $this->server->status = Server::FAILED;

                // TODO: See if there are other strings which are needed
                if (preg_match('/(no tty present|askpass)/', $process->getErrorOutput())) {
                    $this->server->status = Server::FAILED_FPM;
                }
            } else {
                $this->server->status = Server::SUCCESSFUL;
            }
        } catch (\Exception $error) {
            $this->server->status = Server::FAILED;
        }

        $this->server->save();

        unlink($key);
    }
}
