<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Jobs\DeployProject\LogFormatter;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use Symfony\Component\Process\Process as SymfonyProcess;

/**
 * Tests if a server can successfully be SSHed into.
 */
class TestServerConnection extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var int
     */
    public $timeout = 0;

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
     * @param Process      $process
     * @param Filesystem   $filesystem
     * @param LogFormatter $formatter
     */
    public function handle(Process $process, Filesystem $filesystem, LogFormatter $formatter)
    {
        $this->server->status      = Server::TESTING;
        $this->server->connect_log = null;
        $this->server->save();

        $key = $filesystem->tempnam(storage_path('app/tmp/'), 'key');
        $filesystem->put($key, $this->server->project->private_key);
        $filesystem->chmod($key, 0600);

        $prefix = $this->server->id . '_' . $this->server->project_id;
        $output = '';

        $process->setScript('TestServerConnection', [
            'server_id'      => $this->server->id,
            'project_path'   => $this->server->clean_path,
            'test_file'      => $prefix . '_testing_deployer.txt',
            'test_directory' => $prefix . '_testing_deployer_dir',
        ])->setServer($this->server, $key)->run(function ($type, $output_line) use (&$output, $formatter) {
            if ($type === SymfonyProcess::ERR) {
                $output .= $formatter->error($output_line);
            } else {
                $output .= $formatter->info($output_line);
            }

            $this->server->connect_log = $output;
            $this->server->save();
        });

        if (!$process->isSuccessful()) {
            $this->server->status = Server::FAILED;
        } else {
            $this->server->status      = Server::SUCCESSFUL;
            $this->server->connect_log = null;
        }

        $this->server->save();

        $filesystem->delete($key);
    }
}
