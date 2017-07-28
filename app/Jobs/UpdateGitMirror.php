<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Services\Scripts\Parser;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use RuntimeException;

/**
 * Updates the git mirror for a project.
 * This is not queued as DeployProject needs it to run in sequence.
 */
class UpdateGitMirror extends Job
{
    use SerializesModels, DispatchesJobs;

    /**
     * @var int
     */
    public $timeout = 0;

    /**
     * @var Project
     */
    private $project;

    /**
     * UpdateGitMirror constructor.
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
     * @param Process    $process
     * @param Parser     $parser
     * @param Filesystem $filesystem
     */
    public function handle(Process $process, Parser $parser, Filesystem $filesystem)
    {
        $private_key = $filesystem->tempnam(storage_path('app/tmp/'), 'key');
        $filesystem->put($private_key, $this->project->private_key);
        $filesystem->chmod($private_key, 0600);

        $wrapper = $parser->parseFile('tools.SSHWrapperScript', [
            'private_key' => $private_key,
        ]);

        $wrapper_file = $filesystem->tempnam(storage_path('app/tmp/'), 'ssh');
        $filesystem->put($wrapper_file, $wrapper);
        $filesystem->chmod($wrapper_file, 0755);

        $this->project->is_mirroring = true;
        $this->project->save();

        $process->setScript('tools.MirrorGitRepository', [
            'wrapper_file' => $wrapper_file,
            'mirror_path'  => $this->project->mirrorPath(),
            'repository'   => $this->project->repository,
        ])->run();

        $successful = $process->isSuccessful();

        $filesystem->delete([$wrapper_file, $private_key]);

        if ($successful) {
            $this->project->last_mirrored = $this->project->freshTimestamp();
        }

        $this->project->is_mirroring  = false;
        $this->project->save();

        if (!$successful) {
            throw new RuntimeException('Could not mirror repository - ' . $process->getErrorOutput());
        }

        $this->dispatch(new UpdateGitReferences($this->project));
    }
}
