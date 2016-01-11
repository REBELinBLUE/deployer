<?php

namespace REBELinBLUE\Deployer\Jobs;

use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Jobs\Job;
use REBELinBLUE\Deployer\Project;

/**
 * Send email notifications for deployment.
 */
class MailDeployNotification extends Job
{
    private $project;
    private $deployment;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Project $project, Deployment $deployment)
    {
        $this->project    = $project;
        $this->deployment = $deployment;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $emails = $this->project->notifyEmails;

        if ($emails->count() > 0) {
            $status = strtolower($this->project->getPresenter()->readable_status);

            $subject = Lang::get(
                'notifyEmails.subject',
                ['status' => $status, 'project' => $this->project->name]
            );

            $deploymentArr                = $this->deployment->toArray();
            $deploymentArr['commitURL']   = $this->deployment->commit_url;
            $deploymentArr['shortCommit'] = $this->deployment->short_commit;

            $data = [
                'project'    => $this->project->toArray(),
                'deployment' => $deploymentArr,
            ];

            Mail::queueOn(
                'deployer-low',
                'emails.deployed',
                $data,
                function (Message $message) use ($emails, $subject) {
                    foreach ($emails as $email) {
                        $message->to($email->email, $email->name);
                    }

                    $message->subject($subject);
                }
            );
        }
    }
}
