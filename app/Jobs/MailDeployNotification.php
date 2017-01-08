<?php

namespace REBELinBLUE\Deployer\Jobs;

use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Notifications\DeploymentFinished;
use REBELinBLUE\Deployer\Project;

/**
 * Send email notifications for deployment.
 */
class MailDeployNotification extends Job
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * MailDeployNotification constructor.
     *
     * @param Project    $project
     * @param Deployment $deployment
     */
    public function __construct(Project $project, Deployment $deployment)
    {
        $this->project    = $project;
        $this->deployment = $deployment;
    }

    /**
     * Execute the command.
     */
    public function handle()
    {
        $this->project->notifyEmails->each(function ($email) {
            $email->notify(new DeploymentFinished($this->project, $this->deployment));
        });
    }
}
