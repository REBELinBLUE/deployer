<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;

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
     * @param Process    $process
     * @param Filesystem $filesystem
     */
    public function handle(Process $process, Filesystem $filesystem)
    {
        $this->server->status = Server::TESTING;
        $this->server->save();

        $key = $filesystem->tempnam(storage_path('app/tmp/'), 'key');
        $filesystem->put($key, $this->server->project->private_key);
        $filesystem->chmod($key, 0600);

        $prefix = $this->server->id . '_' . $this->server->project_id;

        try {
            $process->setScript('TestServerConnection', [
                'server_id'      => $this->server->id,
                'project_path'   => $this->server->clean_path,
                'test_file'      => $prefix . '_testing_deployer.txt',
                'test_directory' => $prefix . '_testing_deployer_dir',
            ])->setServer($this->server, $key)->run();

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

        $filesystem->delete($key);
    }
}
