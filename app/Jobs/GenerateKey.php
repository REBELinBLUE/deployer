<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use RuntimeException;

/**
 * Job to generate SSH key.
 */
class GenerateKey extends Job
{
    use Dispatchable, SerializesModels;

    /**
     * @var Project
     */
    private $project;

    /**
     * Create a new job instance.
     *
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Execute the job.
     *
     * @param Filesystem $filesystem
     * @param Process    $process
     *
     * @throws RuntimeException
     */
    public function handle(Filesystem $filesystem, Process $process)
    {
        $private_key_file = $filesystem->tempnam(storage_path('app/tmp'), 'key');
        $public_key_file  = $private_key_file . '.pub';

        $process->setScript('tools.GenerateSSHKey', [
            'key_file' => $private_key_file,
            'project'  => $this->project->name,
        ])->run();

        $files = [$private_key_file, $public_key_file];

        if (!$process->isSuccessful()) {
            $filesystem->delete($files);
            throw new RuntimeException($process->getErrorOutput());
        }

        $this->project->private_key = $filesystem->get($private_key_file);
        $this->project->public_key  = $filesystem->get($public_key_file);

        $filesystem->delete($files);
    }
}
