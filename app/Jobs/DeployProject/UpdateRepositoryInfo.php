<?php

namespace REBELinBLUE\Deployer\Jobs\DeployProject;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use RuntimeException;

/**
 * Gets the info for the latest release.
 */
class UpdateRepositoryInfo
{
    use Dispatchable, SerializesModels;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * Create a new job instance.
     *
     * @param Deployment $deployment
     */
    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
    }

    /**
     * Execute the job.
     *
     * @param Process                 $process
     * @param UserRepositoryInterface $repository
     */
    public function handle(Process $process, UserRepositoryInterface $repository)
    {
        $commit = ($this->deployment->commit === Deployment::LOADING ? null : $this->deployment->commit);

        $process->setScript('tools.GetCommitDetails', [
            'deployment'    => $this->deployment->id,
            'mirror_path'   => $this->deployment->project->mirrorPath(),
            'git_reference' => $commit ?: $this->deployment->branch,
        ])->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException('Could not get repository info - ' . $process->getErrorOutput());
        }

        $git_info = $process->getOutput();

        list($commit, $committer, $email) = explode("\x09", $git_info);

        $this->deployment->commit          = trim($commit);
        $this->deployment->committer       = trim($committer);
        $this->deployment->committer_email = trim($email);

        if (!$this->deployment->user_id && !$this->deployment->source) {
            $user = $repository->findByEmail($this->deployment->committer_email);

            if ($user) {
                $this->deployment->user_id = $user->id;
            }
        }

        $this->deployment->save();
    }
}
