<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Jobs\Job;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Ref;
use Symfony\Component\Process\Process;

/**
 * Updates the list of tags and branches in a project.
 */
class UpdateGitReferences extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $project;

    /**
     * Create a new job instance.
     *
     * @param  Project $project The project to update
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
        $mirror_dir = $this->project->mirrorPath();

        $this->project->refs()->delete();

        foreach (['tag', 'branch'] as $ref) {
            $process = new Process("cd {$mirror_dir} && git {$ref} --list --no-column");
            $process->setTimeout(null);
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
