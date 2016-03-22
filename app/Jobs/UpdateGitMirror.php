<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Jobs\Job;
use REBELinBLUE\Deployer\Jobs\UpdateGitReferences;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Scripts\Parser as ScriptParser;
use REBELinBLUE\Deployer\Scripts\Runner as Process;

/**
 * Updates the git mirror for a project.
 */
class UpdateGitMirror extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    private $project;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $private_key = tempnam(storage_path('app/'), 'sshkey');
        file_put_contents($private_key, $this->project->private_key);

        $wrapper = with(new ScriptParser)->parseFile('tools.SSHWrapperScript', [
            'private_key' => $private_key,
        ]);

        $wrapper_file = tempnam(storage_path('app/'), 'gitssh');
        file_put_contents($wrapper_file, $wrapper);

        $process = new Process('tools.MirrorGitRepository', [
            'wrapper_file' => $wrapper_file,
            'mirror_path'  => $this->project->mirrorPath(),
            'repository'   => $this->project->repository,
        ]);
        $process->run();

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
