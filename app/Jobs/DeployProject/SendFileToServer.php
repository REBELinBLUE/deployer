<?php

namespace REBELinBLUE\Deployer\Jobs\DeployProject;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Exceptions\FailedDeploymentException;
use REBELinBLUE\Deployer\ServerLog;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use Symfony\Component\Process\Process as SymfonyProcess;

/**
 * Job to send a file to a server.
 */
class SendFileToServer
{
    use Dispatchable, SerializesModels;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var ServerLog
     */
    private $log;

    /**
     * @var string
     */
    private $local_file;

    /**
     * @var string
     */
    private $remote_file;

    /**
     * @var string
     */
    private $key;

    /**
     * Create a new job instance.
     *
     * @param Deployment $deployment
     * @param ServerLog  $log
     * @param string     $local_file
     * @param string     $remote_file
     * @param string     $key
     */
    public function __construct(Deployment $deployment, ServerLog $log, $local_file, $remote_file, $key)
    {
        $this->deployment  = $deployment;
        $this->local_file  = $local_file;
        $this->remote_file = $remote_file;
        $this->key         = $key;
        $this->log         = $log;
    }

    /**
     * Execute the job.
     *
     * @param Process      $process
     * @param LogFormatter $formatter
     *
     * @throws FailedDeploymentException
     */
    public function handle(Process $process, LogFormatter $formatter)
    {
        $server = $this->log->server;

        $output = '';
        $process->setScript('deploy.SendFileToServer', [
            'deployment'  => $this->deployment->id,
            'port'        => $server->port,
            'private_key' => $this->key,
            'local_file'  => $this->local_file,
            'remote_file' => $this->remote_file,
            'username'    => $server->user,
            'ip_address'  => $server->ip_address,
        ])->run(function ($type, $output_line) use (&$output, $formatter) {
            if ($type === SymfonyProcess::ERR) {
                $output .= $formatter->error($output_line);
            } else {
                // Switching sent/received around
                $output_line = str_replace('received', 'xxx', $output_line);
                $output_line = str_replace('sent', 'received', $output_line);
                $output_line = str_replace('xxx', 'sent', $output_line);

                $output .= $formatter->info($output_line);
            }

            $this->log->output = $output;
            $this->log->save();
        });

        if (!$process->isSuccessful()) {
            throw new FailedDeploymentException($process->getErrorOutput());
        }
    }
}
