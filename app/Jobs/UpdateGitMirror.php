<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Services\Scripts\Parser as ScriptParser;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;

/**
 * Updates the git mirror for a project.
 */
class UpdateGitMirror extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

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
     * @param Process $process
     */
    public function handle(Process $process)
    {
        $private_key = tempnam(storage_path('app/tmp/'), 'sshkey');
        file_put_contents($private_key, $this->project->private_key);
        chmod($private_key, 0600);

        $wrapper = with(new ScriptParser())->parseFile('tools.SSHWrapperScript', [
            'private_key' => $private_key,
        ]);

        $wrapper_file = tempnam(storage_path('app/tmp/'), 'gitssh');
        file_put_contents($wrapper_file, $wrapper);
        chmod($wrapper_file, 0755);

        $process->setScript('tools.MirrorGitRepository', [
            'wrapper_file' => $wrapper_file,
            'mirror_path'  => $this->project->mirrorPath(),
            'repository'   => $this->project->repository,
        ])->run();

        unlink($wrapper_file);
        unlink($private_key);

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Could not mirror repository - ' . $process->getErrorOutput());
        }

        $this->project->last_mirrored = date('Y-m-d H:i:s');
        $this->project->save();

        $this->dispatch(new UpdateGitReferences($this->project));
    }
}
