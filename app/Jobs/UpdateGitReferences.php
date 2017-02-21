<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\RefRepositoryInterface;
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
     * @param Process                $process
     * @param RefRepositoryInterface $repository
     */
    public function handle(Process $process, RefRepositoryInterface $repository)
    {
        $mirror_dir = $this->project->mirrorPath();

        $this->project->refs()->delete();

        foreach (['tag', 'branch'] as $ref) {
            $process->setScript('tools.ListGitReferences', [
                'mirror_path'   => $mirror_dir,
                'git_reference' => $ref,
            ])->run();

            if ($process->isSuccessful()) {
                foreach (explode(PHP_EOL, trim($process->getOutput())) as $reference) {
                    $reference = trim($reference);

                    if (empty($reference)) {
                        continue;
                    }

                    if (starts_with($reference, '*')) {
                        $reference = trim(substr($reference, 1));
                    }

                    $repository->create([
                        'name'       => $reference,
                        'project_id' => $this->project->id,
                        'is_tag'     => ($ref === 'tag'),
                    ]);
                }
            }
        }
    }
}
