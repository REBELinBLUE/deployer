<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Ref;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;

/**
 * Updates the list of tags and branches in a project.
 */
class UpdateGitReferences extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var Project
     */
    private $project;

    /**
     * UpdateGitReferences constructor.
     *
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $mirror_dir = $this->project->mirrorPath();

        $this->project->refs()->delete();

        foreach (['tag', 'branch'] as $ref) {
            $process = new Process('tools.ListGitReferences', [
                'mirror_path'   => $mirror_dir,
                'git_reference' => $ref,
            ]);
            $process->run();

            if ($process->isSuccessful()) {
                foreach (explode(PHP_EOL, trim($process->getOutput())) as $reference) {
                    $reference = trim($reference);

                    if (empty($reference)) {
                        continue;
                    }

                    if (substr($reference, 0, 1) === '*') {
                        $reference = trim(substr($reference, 1));
                    }

                    Ref::create([
                        'name'       => $reference,
                        'project_id' => $this->project->id,
                        'is_tag'     => ($ref === 'tag'),
                    ]);
                }
            }
        }
    }
}
