<?php

namespace REBELinBLUE\Deployer\Jobs\DeployProject;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use RuntimeException;

/**
 * Job to create the archive for a release.
 */
class ReleaseArchiver
{
    use Dispatchable, SerializesModels;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var string
     */
    private $path;

    /**
     * Create a new job instance.
     *
     * @param Deployment $deployment
     * @param string     $path
     */
    public function __construct(Deployment $deployment, $path)
    {
        $this->deployment = $deployment;
        $this->path       = $path;
    }

    /**
     * Execute the job.
     *
     * @param Process $process
     *
     * @throws \RuntimeException
     */
    public function handle(Process $process)
    {
        $tmp_dir = 'clone_' . $this->deployment->project_id . '_' . $this->deployment->release_id;

        $process->setScript('deploy.CreateReleaseArchive', [
            'deployment'      => $this->deployment->id,
            'mirror_path'     => $this->deployment->project->mirrorPath(),
            'scripts_path'    => resource_path('scripts/'),
            'tmp_path'        => storage_path('app/tmp/' . $tmp_dir),
            'sha'             => $this->deployment->commit,
            'release_archive' => storage_path('app/' . $this->path),
        ])->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException('Could not get repository info - ' . $process->getErrorOutput());
        }
    }
}
