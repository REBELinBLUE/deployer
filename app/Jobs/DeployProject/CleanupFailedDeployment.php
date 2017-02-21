<?php

namespace REBELinBLUE\Deployer\Jobs\DeployProject;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;

/**
 * Remove left over artifacts from a failed deploy on each server.
 */
class CleanupFailedDeployment
{
    use Dispatchable, SerializesModels;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var string
     */
    private $release_archive;

    /**
     * @var string
     */
    private $private_key;

    /**
     * Create a new job instance.
     *
     * @param Deployment $deployment
     * @param string     $release_archive
     * @param string     $private_key
     */
    public function __construct(Deployment $deployment, $release_archive, $private_key)
    {
        $this->deployment      = $deployment;
        $this->release_archive = $release_archive;
        $this->private_key     = $private_key;
    }

    /**
     * Execute the job.
     *
     * @param Process $process
     */
    public function handle(Process $process)
    {
        $this->deployment->project->servers->filter(function (Server $server) {
            return $server->deploy_code;
        })->each(function (Server $server) use ($process) {
            $process->setScript('deploy.CleanupFailedRelease', [
                'deployment'     => $this->deployment->id,
                'project_path'   => $server->clean_path,
                'release_path'   => $server->clean_path . '/releases/' . $this->deployment->release_id,
                'remote_archive' => $server->clean_path . '/' . $this->release_archive,
            ])->setServer($server, $this->private_key)->run();
        });
    }
}
